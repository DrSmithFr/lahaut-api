<?php

namespace App\Service\Fly;

use App\Entity\Fly\Slot;
use App\Entity\Fly\SlotLock;
use App\Exception\SlotLock\LockConflictException;
use App\Exception\SlotLock\LockExpiredException;
use App\Repository\Fly\SlotRepository;
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
