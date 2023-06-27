<?php

namespace App\Controller;

use App\Entity\Activity\Slot;
use App\Entity\User;
use App\Form\Activity\AddSlotsType;
use App\Form\Activity\RemoveSlotsType;
use App\Model\Activity\AddSlotsModel;
use App\Model\Activity\RemoveSlotsModel;
use App\Repository\Activity\ActivityTypeRepository;
use App\Repository\Activity\SlotRepository;
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
 * @OA\Tag(name="Activity - Slots")
 */
class SlotController extends AbstractApiController
{
    /**
     * Retrieve all Activity slots of the connected monitor for the given period
     * @OA\Response(
     *     response=200,
     *     description="Retrieve all Activity slots of the connected monitor for the given period",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class, groups={"Default", "monitor"}))
     *     )
     * )
     */
    #[Route(
        path: '/slots/{start<\d{4}-\d{2}-\d{2}>}-{end<\d{4}-\d{2}-\d{2}>}',
        name: 'app_slots_in_period',
        requirements: ['date' => '\d{4}-\d{2}-\d{2}'],
        methods: ['GET']
    )]
    public function listSlotsInPeriod(
        Request $request,
        SlotRepository $slotRepository,
        DateService $dateService,
    ): JsonResponse {
        $start = $dateService->createFromDateString($request->get('start'));
        $end = $dateService->createFromDateString($request->get('end'));

        if (!$start || !$end) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        if ($start > $end) {
            return $this->messageResponse('Invalid period', Response::HTTP_BAD_REQUEST);
        }

        $monitor = $this->getUser();

        $slots = $slotRepository
            ->findUnlockedBetween(
                $start,
                $end->modify('+1 day'),
                null,
                $monitor
            );

        return $this->serializeResponse($slots, ['Default', 'monitor']);
    }

    /**
     * Add a slot to the current user slot list for the current period
     * @OA\RequestBody(@Model(type=AddSlotsModel::class))
     * @OA\Response(
     *     response=200,
     *     description="Returns all Activity slots created",
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

        $end = $dateService
            ->createFromDateString($request->get('end'))
            ->setTime(23, 59, 59);

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
                    $slotData->getActivityType(),
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
                        ->setActivityType($slotData->getActivityType())
                        ->setStartAt($startAt)
                        ->setEndAt($endAt);
                }

                $slot
                    ->setAverageActivityDuration($slotData->getAverageActivityDuration())
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

        $end = $dateService
            ->createFromDateString($request->get('end'))
            ->setTime(23, 59, 59);

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
    ): void {
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
     * Retrieve all Activity slots for the given day
     * @OA\Response(
     *     response=200,
     *     description="Returns all Activity slots for the given day",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class, groups={"Default", "monitor"}))
     *     )
     * )
     */
    #[Route(
        path: '/public/slots/{type}/{date<\d{4}-\d{2}-\d{2}>}',
        name: 'app_slots_list',
        methods: ['get']
    )]
    public function listAllSlots(
        Request $request,
        ActivityTypeRepository $activityTypeRepository,
        SlotRepository $slotRepository,
        DateService $dateService,
    ): JsonResponse {
        $date = $dateService->createFromDateString($request->get('date'));

        if (!$date) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        $activityType = $activityTypeRepository->findOneByIdentifier($request->get('type'));

        if (!$activityType) {
            return $this->messageResponse('Invalid location', Response::HTTP_BAD_REQUEST);
        }

        $slots = $slotRepository
            ->findUnlockedBetween(
                $date,
                $date->setTime(23, 59, 59),
                $activityType,
            );

        return $this->serializeResponse($slots, ['Default', 'monitor', 'ActivityType' => ['Default', 'location']]);
    }

    /**
     * Retrieve all Activity slots of a monitor for the given day
     * @OA\Response(
     *     response=200,
     *     description="Returns all Activity slots of a monitor for the given day",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class, groups={"Default", "monitor"}))
     *     )
     * )
     */
    #[Route(
        path: '/public/slots/{uuid}/{type}/{date<\d{4}-\d{2}-\d{2}>}',
        name: 'app_slots_list_by_monitor',
        requirements: ['date' => '\d{4}-\d{2}-\d{2}'],
        methods: ['GET']
    )]
    public function listMonitorSlots(
        #[MapEntity(class: User::class)] User $monitor,
        Request $request,
        ActivityTypeRepository $activityTypeRepository,
        SlotRepository $slotRepository,
        DateService $dateService,
    ): JsonResponse {
        $date = $dateService->createFromDateString($request->get('date'));

        if (!$date) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        $activityType = $activityTypeRepository->findOneByIdentifier($request->get('type'));

        if (!$activityType) {
            return $this->messageResponse('Invalid location', Response::HTTP_BAD_REQUEST);
        }

        $slots = $slotRepository
            ->findUnlockedBetween(
                $date,
                $date->setTime(23, 59, 59),
                $activityType,
                $monitor
            );

        return $this->serializeResponse($slots, ['Default', 'monitor']);
    }

    /**
     * Retrieve slot by id
     * @OA\Response(
     *     response=200,
     *     description="Returns the slot data",
     *     @Model(type=Slot::class, groups={"Default", "monitor", "ActivityLocation"={"Default", "details"}})
     * )
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
        return $this->serializeResponse($slot, ['Default', 'monitor', 'activityLocation' => ['Default', 'details']]);
    }
}
