<?php

namespace App\Repository\Fly;

use App\Entity\Fly\FlyLocation;
use App\Entity\Fly\SlotProposed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SlotProposed|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlotProposed|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<SlotProposed>    findAll()
 * @method Collection<SlotProposed>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotProposedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SlotProposed::class);
    }

    /**
     * @param FlyLocation       $location
     * @return array<SlotProposed>
     */
    public function findAllByLocation(
        FlyLocation $location,
    ): array {
        return $this
            ->createQueryBuilder('slot')
            ->join('slot.flyType', 'type')
            ->andWhere('type.flyLocation = :location')
            ->setParameter('location', $location)
            ->orderBy('slot.startAt', 'ASC')
            ->addOrderBy('slot.endAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
