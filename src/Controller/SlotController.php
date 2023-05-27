<?php

namespace App\Controller;

use App\Entity\Fly\Slot;
use App\Entity\User;
use App\Enum\FlyTypeEnum;
use App\Form\Fly\AddSlotsType;
use App\Model\Fly\AddSlotsModel;
use App\Model\Fly\SlotModel;
use App\Repository\Fly\FlyLocationRepository;
use App\Repository\Fly\SlotRepository;
use DateTimeImmutable;
use DateTimeInterface;
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
 * @OA\Tag(name="Fly Slots")
 */
class SlotController extends AbstractApiController
{
    /**
     * Add a new slot to the current user slot list
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
    #[Route(path: '/slots', name: 'app_slots_new', methods: ['put'])]
    #[IsGranted('ROLE_MONITOR')]
    public function createSlot(
        Request $request,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $data = new AddSlotsModel();

        $form = $this->handleJsonFormRequest(
            $request,
            AddSlotsType::class,
            $data
        );

        if (!$form->isValid()) {
            return $this->formErrorResponse($form, Response::HTTP_BAD_REQUEST);
        }

        $slots = $data
            ->getSlots()
            ->map(
                fn(SlotModel $slot) => (new Slot())
                    ->setMonitor($this->getUser())
                    ->setFlyLocation($slot->getFlyLocation())
                    ->setStartAt($slot->getStartAt())
                    ->setEndAt($slot->getEndAt())
                    ->setAverageFlyDuration($slot->getAverageFlyDuration())
                    ->setType($slot->getType())
                    ->setPrice($slot->getPrice())
            );

        $slots->forAll(fn(int $key, Slot $slot) => $entityManager->persist($slot));

        $entityManager->flush();

        return $this->serializeResponse($slots, ['Default', 'monitor'], Response::HTTP_CREATED);
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
    ): JsonResponse {
        $type = FlyTypeEnum::tryFrom($request->get('type'));

        if (!$type) {
            return $this->messageResponse('Invalid type', Response::HTTP_BAD_REQUEST);
        }

        // resetting the time to 00:00:00 but keeping current timezone
        $date = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            sprintf('%sT00:00:00P', $request->get('date'))
        );

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
    ): JsonResponse {
        $type = FlyTypeEnum::tryFrom($request->get('type'));

        if (!$type) {
            return $this->messageResponse('Invalid type', Response::HTTP_BAD_REQUEST);
        }

        // resetting the time to 00:00:00 but keeping current timezone
        $date = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            sprintf('%sT00:00:00P', $request->get('date'))
        );

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
