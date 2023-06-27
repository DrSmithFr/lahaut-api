<?php

namespace App\Repository\Slot;

use App\Entity\Slot\Slot;
use App\Entity\Slot\SlotLock;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SlotLock|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlotLock|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<SlotLock>    findAll()
 * @method Collection<SlotLock>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotLockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SlotLock::class);
    }

    /**
     * @param Slot $slot
     * @return Collection<SlotLock>
     */
    public function findActiveLocksForSlot(Slot $slot): Collection
    {
        return $this->createQueryBuilder('slot_lock')
            ->andWhere('slot_lock.slot = :slot')
            ->andWhere('slot_lock.until > :now')
            ->setParameter('slot', $slot)
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function save(SlotLock $lock): void
    {
        $this->getEntityManager()->persist($lock);
        $this->getEntityManager()->flush($lock);
    }

    public function delete(SlotLock $lock)
    {
        $this->getEntityManager()->remove($lock);
        $this->getEntityManager()->flush($lock);
    }
}
