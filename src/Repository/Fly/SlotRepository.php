<?php

namespace App\Repository\Fly;

use App\Entity\Fly\FlyLocation;
use App\Entity\Fly\Slot;
use App\Entity\User;
use App\Enum\FlyTypeEnum;
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
     * @param FlyLocation       $location
     * @param FlyTypeEnum       $type
     * @param User|null         $monitor
     * @return array<Slot>
     */
    public function findUnlockedBetween(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        FlyLocation $location,
        FlyTypeEnum $type,
        ?User $monitor = null
    ): array {
        $qb = $this
            ->createQueryBuilder('slot')
            ->addSelect('monitor')
            ->join('slot.monitor', 'monitor')
            ->andWhere('slot.startAt >= :start')
            ->andWhere('slot.endAt <= :end')
            ->andWhere('slot.flyLocation = :location')
            ->andWhere('slot.type = :type')
            ->setParameters(
                [
                    'start'    => $start,
                    'end'      => $end,
                    'location' => $location,
                    'type'     => $type->value,
                ]
            );

        if ($monitor) {
            $qb
                ->andWhere('monitor = :monitor')
                ->setParameter('monitor', $monitor);
        }

        return $qb
            ->orderBy('slot.startAt', 'ASC')
            ->addOrderBy('slot.endAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     * @param User              $monitor
     * @return array<Slot>
     */
    public function findAllBetween(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        User $monitor
    ): array {
        return $this
            ->createQueryBuilder('slot')
            ->addSelect('booking')
            ->join('slot.monitor', 'monitor')
            ->leftJoin('slot.booking', 'booking')
            ->andWhere('slot.startAt >= :start')
            ->andWhere('slot.endAt <= :end')
            ->andWhere('monitor = :monitor')
            ->setParameters(
                [
                    'start'    => $start,
                    'end'      => $end,
                    'monitor'  => $monitor,
                ]
            )
            ->orderBy('slot.startAt', 'ASC')
            ->addOrderBy('slot.endAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     * @param User              $monitor
     * @return array<Slot>
     */
    public function findAllUnbookedBetween(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        User $monitor
    ): array {
        return $this
            ->createQueryBuilder('slot')
            ->addSelect('booking')
            ->join('slot.monitor', 'monitor')
            ->leftJoin('slot.booking', 'booking')
            ->andWhere('slot.startAt >= :start')
            ->andWhere('slot.endAt <= :end')
            ->andWhere('monitor = :monitor')
            ->andWhere('booking IS NULL')
            ->setParameters(
                [
                    'start'    => $start,
                    'end'      => $end,
                    'monitor'  => $monitor,
                ]
            )
            ->orderBy('slot.startAt', 'ASC')
            ->addOrderBy('slot.endAt', 'ASC')
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
