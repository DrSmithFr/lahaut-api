<?php

namespace App\Repository\Fly\Place;

use App\Entity\Fly\Place\TakeOff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TakeOff|null find($id, $lockMode = null, $lockVersion = null)
 * @method TakeOff|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<TakeOff>    findAll()
 * @method Collection<TakeOff>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TakeOffRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TakeOff::class);
    }
}
