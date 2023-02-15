<?php

namespace App\Service\Fly;

use App\Entity\Fly\Slot;
use App\Entity\Fly\SlotLock;
use App\Entity\User;
use App\Exception\SlotLock\LockConflictException;
use App\Exception\SlotLock\LockExpiredException;
use App\Exception\SlotLock\LockNotAcquiredException;
use App\Repository\Fly\SlotLockRepository;
use DateTimeImmutable;

class SlotLockService
{
    private const LOCK_TTL = 15; // in minute
    private SlotLockRepository $repository;

    public function __construct(SlotLockRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createLock(Slot $slot, User $customer): SlotLock
    {
        $lock = new SlotLock();
        $lock->setSlot($slot);
        $lock->setCustomer($customer);
        $lock->setUntil(new DateTimeImmutable(sprintf('+%s minutes', self::LOCK_TTL)));

        return $lock;
    }

    public function retrieveLock(Slot $slot, User $customer): ?SlotLock
    {
        $locks = $this->repository->findActiveLocksForSlot($slot);

        foreach ($locks as $lock) {
            if ($lock->getCustomer() === $customer) {
                return $lock;
            }
        }

        return null;
    }

    /**
     * @throws LockExpiredException|LockConflictException
     */
    public function acquire(SlotLock $lock): SlotLock
    {
        $otherLock = $this
            ->repository
            ->findActiveLocksForSlot($lock->getSlot());

        foreach ($otherLock as $other) {
            if ($other->getId() !== $lock->getId()) {
                throw new LockConflictException();
            }
        }

        if ($this->isExpired($lock)) {
            throw new LockExpiredException();
        }

        $this->repository->save($lock);

        return $lock;
    }

    public function isAcquired(SlotLock $lock): bool
    {
        $otherLock = $this
            ->repository
            ->findActiveLocksForSlot($lock->getSlot());

        foreach ($otherLock as $other) {
            if ($other->getId() !== $lock->getId()) {
                return false;
            }
        }

        if ($this->isExpired($lock)) {
            throw new LockExpiredException();
        }

        if ($lock->getId() === null) {
            return false;
        }

        return true;
    }

    /**
     * @throws LockNotAcquiredException|LockExpiredException
     */
    public function release(SlotLock $lock): void
    {
        if (!$this->isAcquired($lock)) {
            throw new LockNotAcquiredException();
        }

        $this->repository->delete($lock);
        unset($lock);
    }

    public function isExpired(SlotLock $lock): bool
    {
        return $lock->getUntil() < new DateTimeImmutable();
    }
}
