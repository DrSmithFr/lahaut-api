<?php

namespace App\Service\Activity;

use App\Entity\Activity\Slot;
use App\Entity\Activity\SlotLock;
use App\Exception\SlotLock\LockConflictException;
use App\Exception\SlotLock\LockExpiredException;
use App\Repository\Activity\SlotRepository;
use Doctrine\Common\Collections\ReadableCollection;

class SlotService
{
    private SlotRepository $repository;
    private SlotLockService $lockService;

    public function __construct(SlotRepository $repository, SlotLockService $lockService)
    {
        $this->repository = $repository;
        $this->lockService = $lockService;
    }

    /**
     * @throws LockExpiredException|LockConflictException
     */
    public function lockOverlaps(Slot $slot): ReadableCollection
    {
        return $this
            ->repository
            ->findOverlaps($slot)
            ->map(fn(Slot $slot) => $this->lockService->createLock($slot, $slot->getMonitor()))
            ->map(fn(SlotLock $lock) => $this->lockService->acquire($lock));
    }
}
