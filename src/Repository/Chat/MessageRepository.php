<?php

namespace App\Repository\Chat;

use App\Entity\_Chat\Conversation;
use App\Entity\_Chat\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collection<Message>    findAll()
 * @method Collection<Message>    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param Conversation $conversation
     *
     * @return array
     */
    public function findAllByConversation(Conversation $conversation): array
    {
        return $this
            ->createQueryBuilder('m')
            ->where('m.conversation = :conversation')
            ->setParameter('conversation', $conversation)
            ->getQuery()
            ->getResult();
    }
}
