<?php

namespace App\Repository\Activity\Place;

use App\Entity\Activity\Place\MeetingPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MeetingPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method MeetingPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<MeetingPoint>    findAll()
 * @method Collection<MeetingPoint>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MeetingPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetingPoint::class);
    }

    public function findOneByIdentifier(string $identifier): ?MeetingPoint
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }
}
