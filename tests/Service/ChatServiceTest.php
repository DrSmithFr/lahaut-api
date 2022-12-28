<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\ChatService;
use App\Tests\ApiTestCase;

class ChatServiceTest extends ApiTestCase
{
    private ?ChatService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(ChatService::class);
    }

    public function testCreateNewConversationBetween(): void
    {
        $user1 = (new User())
            ->setEmail('user1');

        $user2 = (new User())
            ->setEmail('user2');

        $conversation = $this->service->createNewConversationBetween([$user1, $user2]);

        $this->assertCount(2, $conversation->getParticipants());
        $this->assertEquals($user1, $conversation->getParticipants()[0]->getUser());
        $this->assertEquals($user2, $conversation->getParticipants()[1]->getUser());
    }

    public function testAddMessageToConversation(): void
    {
        $user = (new User())
            ->setEmail('user');

        $conversation = (new ChatService())->createNewConversationBetween([$user]);

        $message = $this->service->addMessageToConversation($conversation, $user, 'Hello world');

        $this->assertCount(1, $conversation->getMessages());
        $this->assertEquals($message, $conversation->getMessages()[0]);
        $this->assertEquals($message, $conversation->getLastMessage());
    }
}
