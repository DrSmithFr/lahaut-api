<?php

namespace App\Service\Slot;

use App\Entity\Slot\Slot;
use App\Entity\Slot\SlotLock;
use App\Exception\SlotLock\LockConflictException;
use App\Exception\SlotLock\LockExpiredException;
use App\Repository\Slot\SlotRepository;
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
