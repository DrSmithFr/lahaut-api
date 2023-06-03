<?php

namespace App\Controller;

use App\Entity\Fly\SlotProposed;
use App\Repository\Fly\FlyLocationRepository;
use App\Repository\Fly\SlotProposedRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Fly - Slots")
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
        FlyLocationRepository $flyLocationRepository,
        SlotProposedRepository $slotRepository,
    ): JsonResponse {
        $flyLocation = $flyLocationRepository->findOneByIdentifier($request->get('location'));

        if (!$flyLocation) {
            return $this->messageResponse('Invalid location', Response::HTTP_BAD_REQUEST);
        }

        $slots = $slotRepository->findAllByLocation($flyLocation);

        return $this->serializeResponse($slots);
    }
}
