<?php

namespace App\Repository\Activity\Place;

use App\Entity\Activity\Place\LandingPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LandingPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method LandingPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<LandingPoint>    findAll()
 * @method Collection<LandingPoint>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LandingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandingPoint::class);
    }

    public function findOneByIdentifier(string $identifier): ?LandingPoint
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }
}
