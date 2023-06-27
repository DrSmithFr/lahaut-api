<?php

namespace App\Service\Activity;

use App\Entity\Activity\Booking;
use App\Entity\Activity\Slot;
use App\Entity\User;
use App\Enum\BookingStatusEnum;
use App\Exception\Booking\BookingOverlapException;
use App\Exception\SlotLock\LockConflictException;
use App\Exception\SlotLock\LockExpiredException;
use App\Repository\Activity\BookingRepository;

class BookingService
{
    private BookingRepository $repository;
    private SlotService $slotService;

    public function __construct(BookingRepository $repository, SlotService $slotService)
    {
        $this->repository = $repository;
        $this->slotService = $slotService;
    }

    public function createBookingForUser(User $user, Slot $slot): Booking
    {
        $booking = new Booking();

        $booking->setCustomer($user);
        $booking->setSlot($slot);

        return $booking;
    }

    /**
     * @throws BookingOverlapException
     * @throws LockConflictException
     * @throws LockExpiredException
     */
    public function book(Booking $booking): void
    {
        if ($this->isPossible($booking)) {
            throw new BookingOverlapException();
        }

        $this
            ->slotService
            ->lockOverlaps($booking->getSlot());

        $booking->setStatus(BookingStatusEnum::PENDING);
    }

    public function isPossible(Booking $booking)
    {
        $bookings = $this
            ->repository
            ->findOverlapsForMonitor(
                $booking->getSlot()->getMonitor(),
                $booking->getSlot()->getStartAt(),
                $booking->getSlot()->getEndAt()
            );

        return count($bookings) === 0;
    }
}
