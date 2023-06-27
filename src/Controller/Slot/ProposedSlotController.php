<?php

namespace App\Controller\Slot;

use App\Controller\AbstractApiController;
use App\Entity\Slot\SlotProposed;
use App\Repository\Activity\ActivityLocationRepository;
use App\Repository\Slot\SlotProposedRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Slots")
 */
class ProposedSlotController extends AbstractApiController
{
    /**
     * Retrieve all proposed slots for the given location
     * @OA\Response(
     *     response=200,
     *     description="Returns all proposed slots for the given location",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=SlotProposed::class))
     *     )
     * )
     */
    #[Route(
        path: '/public/slots/proposed/{location}',
        name: 'app_slots_proposed_list',
        methods: ['get']
    )]
    public function listAllSlots(
        Request $request,
        ActivityLocationRepository $activityLocationLocationRepository,
        SlotProposedRepository $slotRepository,
    ): JsonResponse {
        $activityLocationLocation = $activityLocationLocationRepository->findOneByIdentifier($request->get('location'));

        if (!$activityLocationLocation) {
            return $this->messageResponse('Invalid location', Response::HTTP_BAD_REQUEST);
        }

        $slots = $slotRepository->findAllByLocation($activityLocationLocation);

        return $this->serializeResponse($slots);
    }
}
