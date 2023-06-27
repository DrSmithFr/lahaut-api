<?php

namespace App\Repository\Activity;

use App\Entity\Activity\ActivityLocation;
use App\Entity\Activity\ActivityType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ActivityType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityType|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<ActivityType>    findAll()
 * @method Collection<ActivityType>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityType::class);
    }

    public function findOneByIdentifier(string $identifier): ?ActivityType
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }

    public function findAllByActivityLocation(ActivityLocation $activityLocation)
    {
        return $this->findBy(['activityLocation' => $activityLocation]);
    }
}
