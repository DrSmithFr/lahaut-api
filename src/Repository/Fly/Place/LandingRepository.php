<?php

namespace App\Repository\Fly\Place;

use App\Entity\Fly\Place\Landing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Landing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Landing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<Landing>    findAll()
 * @method Collection<Landing>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LandingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Landing::class);
    }
}
