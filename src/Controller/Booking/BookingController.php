<?php

namespace App\Controller\Booking;

use App\Controller\AbstractApiController;
use App\Entity\Slot\Slot;
use App\Repository\Booking\BookingRepository;
use App\Service\_Utils\DateService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @OA\Tag(name="Booking")
 */
class BookingController extends AbstractApiController
{
    /**
     * Retrieve all Booking for the given periode
     * @OA\Response(
     *     response=200,
     *     description="Returns all activity slots for the given day",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Slot::class, groups={"Default", "monitor"}))
     *     )
     * )
     */
    #[Route(
        path: '/booking/{start<\d{4}-\d{2}-\d{2}>}-{end<\d{4}-\d{2}-\d{2}>}',
        name: 'app_booking_period_list',
        methods: ['get']
    )]
    #[IsGranted('ROLE_MONITOR')]
    public function listAllSlots(
        Request $request,
        BookingRepository $bookingRepository,
        DateService $dateService,
    ): JsonResponse {
        $start = $dateService->createFromDateString($request->get('start'));
        $end = $dateService->createFromDateString($request->get('end'));

        if (!$start || !$end) {
            return $this->messageResponse('Invalid date', Response::HTTP_BAD_REQUEST);
        }

        $monitor = $this->getUser();

        $bookings = $bookingRepository
            ->findAllBetween(
                $start,
                $end,
                $monitor,
            );

        return $this->serializeResponse($bookings);
    }
}
