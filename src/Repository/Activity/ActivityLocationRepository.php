<?php

namespace App\Repository\Activity;

use App\Entity\Activity\ActivityLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ActivityLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<ActivityLocation>    findAll()
 * @method Collection<ActivityLocation>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityLocation::class);
    }

    public function findOneByIdentifier(string $identifier): ?ActivityLocation
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }
}
