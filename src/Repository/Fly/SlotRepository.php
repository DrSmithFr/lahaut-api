<?php

namespace App\Repository\Fly;

use App\Entity\Fly\Slot;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<Slot>    findAll()
 * @method Collection<Slot>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     * @param User|null         $monitor
     * @return array<Slot>
     */
    public function findUnlockedBetween(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        ?User $monitor = null
    ): array {
        $qb = $this
            ->createQueryBuilder('slot')
            ->andWhere('slot.startAt >= :start')
            ->andWhere('slot.endAt <= :end')
            ->setParameters(
                [
                    'start' => $start,
                    'end'   => $end,
                ]
            );

        if ($monitor) {
            $qb
                ->andWhere('slot.monitor = :monitor')
                ->setParameter('monitor', $monitor);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Slot $slot
     * @return Collection<Slot>
     */
    public function findOverlaps(Slot $slot): Collection
    {
        $qb = $this->createQueryBuilder('slot');

        return $qb
            ->where('slot.monitor = :monitor')
            ->andWhere(
                $qb->expr()->orX(
                // slots contained in the slot duration
                    'slot.startAt >= :start AND slot.endAt <= :end',
                    // slots starting before the slot and overlapping
                    'slot.startAt <= :start AND slot.endAt > :start AND slot.endAt <= :end',
                    // slots starting after the slot and overlapping
                    'slot.startAt >= :start AND slot.startAt < :end AND slot.endAt >= :end',
                )
            )
            ->setParameters(
                [
                    'monitor' => $slot->getMonitor(),
                    'start'   => $slot->getStartAt(),
                    'end'     => $slot->getEndAt(),
                ]
            )
            ->distinct()
            ->getQuery()
            ->getResult();
    }
}
