<?php

namespace App\Repository\Booking;

use App\Entity\Booking\Booking;
use App\Entity\User;
use App\Enum\BookingStatusEnum;
use DateInterval;
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

    public function totalPerDay(int $days)
    {
        $from = (new DateTimeImmutable('now'))
            ->sub(new DateInterval('P' . $days . 'D'))
            ->setTime(0, 0);

        $result = $this
            ->createQueryBuilder('booking')
            ->select('CAST(booking.createdAt as DATE) as day, SUM(slot.price) as total')
            ->join('booking.slot', 'slot')
            ->groupBy('day')
            ->andWhere('booking.createdAt >= :from')
            ->setParameter('from', $from)
            ->getQuery()
            ->getScalarResult();

        $resultMap = array_reduce(
            $result,
            function (array $carry, array $item) {
                $carry[$item['day']] = $item['total'];
                return $carry;
            },
            []
        );

        $totals = [];

        // Fill missing days
        for ($i = $days; $i >= 0; $i--) {
            $day = $from
                ->add(new DateInterval('P' . $i . 'D'))
                ->format('Y-m-d');

            if (isset($resultMap[$day])) {
                $totals[$day] = $resultMap[$day];
            } else {
                $totals[$day] = "0";
            }
        }

        return array_reverse($totals);
    }
}
