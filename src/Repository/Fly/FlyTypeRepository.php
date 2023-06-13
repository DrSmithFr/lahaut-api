<?php

namespace App\Repository\Fly;

use App\Entity\Fly\FlyLocation;
use App\Entity\Fly\FlyType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FlyType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlyType|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<FlyType>    findAll()
 * @method Collection<FlyType>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlyTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlyType::class);
    }

    public function findOneByIdentifier(string $identifier): ?FlyType
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }

    public function findAllByFlyLocation(FlyLocation $flyLocation)
    {
        return $this->findBy(['flyLocation' => $flyLocation]);
    }
}
