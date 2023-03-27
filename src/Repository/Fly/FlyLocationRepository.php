<?php

namespace App\Repository\Fly;

use App\Entity\Fly\FlyLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FlyLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlyLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<FlyLocation>    findAll()
 * @method Collection<FlyLocation>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlyLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlyLocation::class);
    }

    public function findOneByIdentifier(string $identifier): ?FlyLocation
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }
}
