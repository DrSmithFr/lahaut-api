<?php

namespace App\Repository\Chat;

use App\Entity\Chat\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<Participant>    findAll()
 * @method Collection<Participant>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }
}
