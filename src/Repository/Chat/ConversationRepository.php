<?php

namespace App\Repository\Chat;

use App\Entity\Chat\Conversation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<Conversation>    findAll()
 * @method Collection<Conversation>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * @param User[] $users
     *
     * @return Conversation|null
     * @throws NonUniqueResultException
     */
    public function getOneByParticipants(array $users): ?Conversation
    {
        $subQuery = $this->createQueryBuilder('conv')
            ->select('conv.uuid')
            ->leftJoin('conv.participants', 'part')
            ->groupBy('conv.uuid')
            ->having('COUNT(part.user) = :count')
            ->getDQL();

        $qb = $this
            ->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.participants', 'p')
            ->where('c.uuid IN (' . $subQuery . ')')
            ->andWhere('p.user IN (:users)')
            ->setParameters([
                'users' => $users,
                'count' => count($users),
            ]);

        $data = $qb
            ->getQuery()
            ->getOneOrNullResult();

        return $data;
    }

    /**
     * @param User $user
     *
     * @return Collection<Conversation>
     */
    public function findAllByUser(User $user): array
    {
        return $this
            ->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.participants', 'p')
            ->where('p.user IN (:user)')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function isParticipant(User $user, Conversation $conversation): bool
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->innerJoin('c.participants', 'p')
            ->where('c.id = :conversation')
            ->andWhere('p.user = :user')
            ->setParameters([
                'user' => $user,
                'conversation' => $conversation,
            ]);

        $conversation = $qb
            ->getQuery()
            ->getOneOrNullResult();

        return $conversation instanceof Conversation;
    }
}
