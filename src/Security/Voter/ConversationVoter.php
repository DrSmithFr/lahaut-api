<?php

namespace App\Security\Voter;

use App\Entity\_Chat\Conversation;
use App\Entity\User;
use App\Repository\Chat\ConversationRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConversationVoter extends Voter
{
    /**
     * @var ConversationRepository
     */
    private ConversationRepository $conversationRepository;

    public function __construct(ConversationRepository $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Conversation;
    }

    /**
     * @param string         $attribute
     * @param Conversation   $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $this
            ->conversationRepository
            ->isParticipant($user, $subject);
    }
}
