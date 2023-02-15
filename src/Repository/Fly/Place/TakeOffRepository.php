<?php

namespace App\Repository\Fly\Place;

use App\Entity\Fly\Place\TakeOffPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TakeOffPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method TakeOffPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<TakeOffPoint>    findAll()
 * @method Collection<TakeOffPoint>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TakeOffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TakeOffPoint::class);
    }

    public function findOneByIdentifier(string $identifier): ?TakeOffPoint
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }
}
