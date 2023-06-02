<?php

namespace App\Controller;

use App\Entity\Fly\Slot;
use App\Entity\User;
use App\Enum\FlyTypeEnum;
use App\Form\Fly\AddSlotsType;
use App\Form\Fly\RemoveSlotsType;
use App\Model\Fly\AddSlotsModel;
use App\Model\Fly\RemoveSlotsModel;
use App\Model\Fly\SlotModel;
use App\Repository\Fly\BookingRepository;
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
 * @OA\Tag(name="Booking")
 */
class BookingController extends AbstractApiController
{
    /**
     * Retrieve all Booking for the given periode
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
        path: '/booking/{start<\d{4}-\d{2}-\d{2}>}-{end<\d{4}-\d{2}-\d{2}>}',
        name: 'app_booking_period_list',
        methods: ['get']
    )]
    #[IsGranted('ROLE_MONITOR')]
    public function listAllSlots(
        Request $request,
        BookingRepository $bookingRepository,
    ): JsonResponse {
        // resetting the time to 00:00:00 but keeping current timezone
        $start = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            sprintf('%sT00:00:00P', $request->get('start'))
        );

        $end = DateTimeImmutable::createFromFormat(
            DateTimeInterface::ATOM,
            sprintf('%sT00:00:00P', $request->get('end'))
        );

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
