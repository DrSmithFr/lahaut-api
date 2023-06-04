<?php

namespace App\Controller;

use App\Entity\Fly\Slot;
use App\Entity\User;
use App\Enum\FlyTypeEnum;
use App\Form\Fly\AddSlotsType;
use App\Form\Fly\RemoveSlotsType;
use App\Model\Fly\AddSlotsModel;
use App\Model\Fly\RemoveSlotsModel;
use App\Repository\Fly\FlyLocationRepository;
use App\Repository\Fly\SlotRepository;
use App\Service\DateService;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @OA\Tag(name="Fly - Slots")
 */
class SlotController extends AbstractApiController
{
    /**
     * Add a slot to the current user slot list for the current period
     * @OA\RequestBody(@Model(type=AddSlotsModel::class))
     * @OA\Response(
     *     response=200,
     *     description="Returns all fly slots created",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class, groups={"Default", "monitor"}))
     *     )
     * )
     * @OA\Response(response="401", description="Cannot connect user")
     */
    #[Route(
        path: '/slots/{start<\d{4}-\d{2}-\d{2}>}-{end<\d{4}-\d{2}-\d{2}>}',
        name: 'app_slots_add',
        methods: ['put']
    )]
    #[IsGranted('ROLE_MONITOR')]
    public function createSlot(
        Request $request,
        EntityManagerInterface $entityManager,
        DateService $dateService,
        SlotRepository $slotRepository,
    ): JsonResponse {
        $start = $dateService->createFromDateString($request->get('start'));
        $end = $dateService->createFromDateString($request->get('end'));

        if (!$start || !$end) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        if ($start > $end) {
            return $this->messageResponse('Invalid period', Response::HTTP_BAD_REQUEST);
        }

        $data = new AddSlotsModel();

        $form = $this->handleJsonFormRequest(
            $request,
            AddSlotsType::class,
            $data
        );

        if (!$form->isValid()) {
            return $this->formErrorResponse($form, Response::HTTP_BAD_REQUEST);
        }

        $monitor = $this->getUser();

        // Remove all slots in period if wipe is true
        if ($data->isWipe()) {
            $this->removeSlotsByPeriod(
                $slotRepository,
                $entityManager,
                $start,
                $end,
                $this->getUser()
            );
        }

        $slots = new ArrayCollection();

        for ($date = $start; $date <= $end; $date = $date->modify('+1 day')) {
            foreach ($data->getSlots() as $slotData) {
                $startAt = $dateService->createFromTimeString(
                    $slotData->getStartAt()->format('H:i'),
                    $date
                );

                $endAt = $dateService->createFromTimeString(
                    $slotData->getEndAt()->format('H:i'),
                    $date
                );

                $slot = $slotRepository->findMatch(
                    $monitor,
                    $slotData->getFlyLocation(),
                    $slotData->getType(),
                    $startAt,
                    $endAt
                );

                if ($data->isOverwrite() === false && $slot !== null) {
                    // If overwrite is false, we prevent any slot creation if one already exists
                    return $this->messageResponse(
                        'Slot already exists',
                        Response::HTTP_CONFLICT
                    );
                }

                if ($slot === null) {
                    $slot = (new Slot())
                        ->setMonitor($monitor)
                        ->setType($slotData->getType())
                        ->setFlyLocation($slotData->getFlyLocation())
                        ->setStartAt($startAt)
                        ->setEndAt($endAt);
                }

                $slot
                    ->setAverageFlyDuration($slotData->getAverageFlyDuration())
                    ->setPrice($slotData->getPrice());

                $entityManager->persist($slot);
                $slots->add($slot);
            }
        }

        $entityManager->flush();

        return $this->serializeResponse(
            $slots,
            ['Default', 'monitor'],
            Response::HTTP_CREATED
        );
    }

    /**
     * Remove slots of the current user
     * @OA\RequestBody(@Model(type=RemoveSlotsModel::class))
     * @OA\Response(response="202", description="Slots removed")
     * @OA\Response(response="400", description="Cannot remove slots that are not yours")
     * @OA\Response(response="406", description="Cannot remove slots that are already booked")
     */
    #[Route(
        path: '/slots/{start<\d{4}-\d{2}-\d{2}>}-{end<\d{4}-\d{2}-\d{2}>}',
        name: 'app_slots_remove_period',
        methods: ['delete']
    )]
    #[IsGranted('ROLE_MONITOR')]
    public function removeSlotsByPeriodAction(
        Request $request,
        SlotRepository $slotRepository,
        EntityManagerInterface $entityManager,
        DateService $dateService,
    ): JsonResponse {
        $start = $dateService->createFromDateString($request->get('start'));
        $end = $dateService->createFromDateString($request->get('end'));

        if (!$start || !$end) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        $this->removeSlotsByPeriod(
            $slotRepository,
            $entityManager,
            $start,
            $end,
            $this->getUser()
        );

        return $this->messageResponse('slots removed', Response::HTTP_ACCEPTED);
    }

    private function removeSlotsByPeriod(
        SlotRepository $slotRepository,
        EntityManagerInterface $entityManager,
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        User $monitor,
    ) {
        $slots = $slotRepository
            ->findAllUnbookedBetween(
                $start,
                $end,
                $monitor,
            );

        foreach ($slots as $slot) {
            $entityManager->remove($slot);
        }

        $entityManager->flush();
    }

    /**
     * Remove slots of the current user
     * @OA\RequestBody(@Model(type=RemoveSlotsModel::class))
     * @OA\Response(response="202", description="Slots removed")
     * @OA\Response(response="400", description="Cannot remove slots that are not yours")
     * @OA\Response(response="406", description="Cannot remove slots that are already booked")
     */
    #[Route(path: '/slots', name: 'app_slots_remove', methods: ['delete'])]
    #[IsGranted('ROLE_MONITOR')]
    public function removeSlots(
        Request $request,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $data = new RemoveSlotsModel();

        $form = $this->handleJsonFormRequest(
            $request,
            RemoveSlotsType::class,
            $data
        );

        if (!$form->isValid()) {
            return $this->formErrorResponse($form, Response::HTTP_BAD_REQUEST);
        }

        foreach ($data->getSlots() as $slot) {
            if ($slot->getMonitor() !== $this->getUser()) {
                return $this->messageResponse(
                    'cannot remove slots that are not yours',
                    Response::HTTP_BAD_REQUEST
                );
            }

            if ($slot->getBooking() !== null) {
                return $this->messageResponse(
                    'cannot remove slots that are already booked',
                    Response::HTTP_NOT_ACCEPTABLE
                );
            }

            $entityManager->remove($slot);
        }

        $entityManager->flush();

        return $this->messageResponse('slots removed', Response::HTTP_ACCEPTED);
    }

    /**
     * Retrieve all fly slots for the given day
     * @OA\Response(
     *     response=200,
     *     description="Returns all fly slots for the given day",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class, groups={"Default", "monitor"}))
     *     )
     * )
     */
    #[Route(
        path: '/public/slots/{location}/{type<(discovery|freestyle|xl)>}/{date<\d{4}-\d{2}-\d{2}>}',
        name: 'app_slots_list',
        methods: ['get']
    )]
    public function listAllSlots(
        Request $request,
        FlyLocationRepository $flyLocationRepository,
        SlotRepository $slotRepository,
        DateService $dateService,
    ): JsonResponse {
        $type = FlyTypeEnum::tryFrom($request->get('type'));

        if (!$type) {
            return $this->messageResponse('Invalid type', Response::HTTP_BAD_REQUEST);
        }

        $date = $dateService->createFromDateString($request->get('date'));

        if (!$date) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        $flyLocation = $flyLocationRepository->findOneByIdentifier($request->get('location'));

        if (!$flyLocation) {
            return $this->messageResponse('Invalid location', Response::HTTP_BAD_REQUEST);
        }

        $slots = $slotRepository
            ->findUnlockedBetween(
                $date,
                $date->modify('+1 day'),
                $flyLocation,
                $type
            );

        return $this->serializeResponse($slots, ['Default', 'monitor']);
    }

    /**
     * Retrieve all fly slots of a monitor for the given day
     * @OA\Response(
     *     response=200,
     *     description="Returns all fly slots of a monitor for the given day",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class, groups={"Default", "monitor"}))
     *     )
     * )
     * @param User                  $monitor
     * @param Request               $request
     * @param FlyLocationRepository $flyLocationRepository
     * @param SlotRepository        $slotRepository
     * @return JsonResponse
     */
    #[Route(
        path: '/public/slots/{uuid}/{location}/{type<(discovery|freestyle|xl)>}/{date<\d{4}-\d{2}-\d{2}>}',
        name: 'app_slots_list_by_monitor',
        requirements: ['date' => '\d{4}-\d{2}-\d{2}'],
        methods: ['GET']
    )]
    public function listMonitorSlots(
        #[MapEntity(class: User::class)] User $monitor,
        Request $request,
        FlyLocationRepository $flyLocationRepository,
        SlotRepository $slotRepository,
        DateService $dateService,
    ): JsonResponse {
        $type = FlyTypeEnum::tryFrom($request->get('type'));

        if (!$type) {
            return $this->messageResponse('Invalid type', Response::HTTP_BAD_REQUEST);
        }

        $date = $dateService->createFromDateString($request->get('date'));

        if (!$date) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        $flyLocation = $flyLocationRepository->findOneByIdentifier($request->get('location'));

        if (!$flyLocation) {
            return $this->messageResponse('Invalid location', Response::HTTP_BAD_REQUEST);
        }

        $slots = $slotRepository
            ->findUnlockedBetween(
                $date,
                $date->modify('+1 day'),
                $flyLocation,
                $type,
                $monitor
            );

        return $this->serializeResponse($slots, ['Default', 'monitor']);
    }

    /**
     * Retrieve slot by id
     * @OA\Response(
     *     response=200,
     *     description="Returns the slot data",
     *     @Model(type=Slot::class, groups={"Default", "monitor", "flyLocation"={"Default", "details"}})
     * )
     * @param Slot $slot
     * @return JsonResponse
     */
    #[Route(
        path: '/public/slots/{id}',
        name: 'app_slot_by_id',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getSlot(
        #[MapEntity(class: Slot::class)] Slot $slot,
    ): JsonResponse {
        return $this->serializeResponse($slot, ['Default', 'monitor', 'flyLocation' => ['Default', 'details']]);
    }
}
