<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /***
     * @param string $identifier
     *
     * @return UserInterface|null
     * @throws NonUniqueResultException
     */
    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->findOneActiveByEmail($identifier);
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->addSelect('r')
            ->addSelect('g')
            ->addSelect('gr')
            ->from(User::class, 'u')
            ->leftJoin('u.roles', 'r')
            ->leftJoin('u.groups', 'g')
            ->leftJoin('g.roles', 'gr')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :mail')
            ->setParameter('mail', strtolower($email))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneActiveByEmail(string $email): ?User
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.email = :mail')
            ->andWhere('u.deletedAt IS NULL')
            ->andWhere('u.enable = true')
            ->setParameter('mail', strtolower($email))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getUserByPasswordResetToken(string $resetToken): ?User
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.passwordResetToken = :token')
            ->setParameter('token', $resetToken)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
