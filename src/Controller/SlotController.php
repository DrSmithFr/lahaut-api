<?php

namespace App\Controller;

use App\Entity\Fly\Slot;
use App\Entity\User;
use App\Form\Fly\AddSlotsType;
use App\Model\Fly\AddSlotsModel;
use App\Model\Fly\SlotModel;
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
     * @OA\RequestBody(
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class))
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns all fly slots created",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class))
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
                    ->setMeetingPoint($slot->getMeetingPoint())
                    ->setTakeOffPoint($slot->getTakeOffPoint())
                    ->setLandingPoint($slot->getLandingPoint())
                    ->setStartAt($slot->getStartAt())
                    ->setEndAt($slot->getEndAt())
                    ->setAverageFlyDuration($slot->getAverageFlyDuration())
                    ->setType($slot->getType())
            );

        $slots->forAll(fn(int $key, Slot $slot) => $entityManager->persist($slot));

        $entityManager->flush();

        return $this->serializeResponse($slots, ['Default'], Response::HTTP_CREATED);
    }


    /**
     * Retrieve all fly slots for the given day
     * @OA\Response(
     *     response=200,
     *     description="Returns all fly slots for the given day",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class))
     *     )
     * )
     */
    #[Route(
        path: '/slots/{date}',
        name: 'app_slots_list',
        requirements: ['date' => '\d{4}-\d{2}-\d{2}'],
        methods: ['get']
    )]
    #[IsGranted('ROLE_USER')]
    public function listAllSlots(
        Request $request,
        SlotRepository $slotRepository,
    ): JsonResponse {
        // resetting the time to 00:00:00 but keeping current timezone
        $date = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            sprintf('%sT00:00:00P', $request->get('date'))
        );

        if (!$date) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        $slots = $slotRepository
            ->findUnlockedBetween(
                $date,
                $date->modify('+1 day')
            );

        return $this->serializeResponse($slots);
    }

    /**
     * Retrieve all fly slots of a monitor for the given day
     * @OA\Response(
     *     response=200,
     *     description="Returns all fly slots of a monitor for the given day",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class))
     *     )
     * )
     * @param Request        $request
     * @param User           $monitor
     * @param SlotRepository $slotRepository
     * @return JsonResponse
     */
    #[Route(
        path: '/slots/{uuid}/{date}',
        name: 'app_conversation_messages',
        requirements: ['date' => '\d{4}-\d{2}-\d{2}'],
        methods: ['GET']
    )]
    #[IsGranted('ROLE_USER')]
    public function listMonitorSlots(
        Request $request,
        #[MapEntity(class: User::class)] User $monitor,
        SlotRepository $slotRepository
    ): JsonResponse {
        // resetting the time to 00:00:00 but keeping current timezone
        $date = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            sprintf('%sT00:00:00P', $request->get('date'))
        );

        if (!$date) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        $slots = $slotRepository
            ->findUnlockedBetween(
                $date,
                $date->modify('+1 day'),
                $monitor
            );

        return $this->serializeResponse($slots);
    }
}