<?php

namespace App\Repository\Booking;

use App\Entity\Booking\Booking;
use App\Entity\User;
use App\Enum\BookingStatusEnum;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

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
     * @param User $monitor
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
                    'start' => $startAt,
                    'end' => $endAt,
                ]
            )
            ->getQuery()
            ->getResult();
    }

    public function findAllBetween(DateTimeImmutable $start, DateTimeImmutable $end, ?User $monitor)
    {
        $qb = $this
            ->createQueryBuilder('booking')
            ->join('booking.slot', 'slot')
            ->where('slot.startAt >= :start')
            ->andWhere('slot.endAt <= :end')
            ->setParameters(
                [
                    'start' => $start,
                    'end' => $end,
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

    public function totalAmountThisMonth()
    {
        $today = (new DateTimeImmutable('now'))
            ->setTime(23, 59, 59);

        $firstDayThisMonth = $today
            ->setDate($today->format('Y'), $today->format('m'), 1)
            ->setTime(0, 0);

        return $this
            ->createQueryBuilder('booking')
            ->select('SUM(slot.price)')
            ->join('booking.slot', 'slot')
            ->where('booking.createdAt BETWEEN :start AND :end')
            ->andWhere('booking.status IN (:status)')
            ->setParameters([
                'start' => $firstDayThisMonth,
                'end' => $today,
                'status' => [
                    BookingStatusEnum::PAID,
                    BookingStatusEnum::CONFIRMED,
                    BookingStatusEnum::TERMINATED,
                ]
            ])
            ->getQuery()
            ->getSingleScalarResult() ?? 0.0;
    }
}
