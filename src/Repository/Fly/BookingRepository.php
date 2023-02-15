<?php

namespace App\Repository\Fly;

use App\Entity\Fly\Booking;
use App\Entity\User;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<Booking>    findAll()
 * @method Collection<Booking>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @param User     $monitor
     * @param DateTime $startAt
     * @param DateTime $endAt
     *
     * @return array<Booking>
     */
    public function findOverlapsForMonitor(User $monitor, DateTimeInterface $startAt, DateTimeInterface $endAt): array
    {
        $qb = $this->createQueryBuilder('booking');

        return $qb
            ->innerJoin('booking.slot', 'slot')
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
                    'monitor' => $monitor,
                    'start'   => $startAt,
                    'end'     => $endAt,
                ]
            )
            ->getQuery()
            ->getResult();
    }
}
