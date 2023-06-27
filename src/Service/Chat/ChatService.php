<?php

declare(strict_types=1);

namespace App\Service\Chat;

use App\Entity\Chat\Conversation;
use App\Entity\Chat\Message;
use App\Entity\Chat\Participant;
use App\Entity\User;
use DateTimeImmutable;

class ChatService
{
    public function createNewConversationBetween(array $users): Conversation
    {
        $conversation = new Conversation();

        foreach ($users as $user) {
            $participant = new Participant();
            $participant->setUser($user);
            $participant->setConversation($conversation);
        }

        return $conversation;
    }

    public function addMessageToConversation(Conversation $conversation, User $user, string $content): Message
    {
        $message = (new Message())
            ->setContent($content)
            ->setUser($user)
            ->setSentAt(new DateTimeImmutable());

        $conversation->addMessage($message);
        $conversation->setLastMessage($message);

        return $message;
    }
}
