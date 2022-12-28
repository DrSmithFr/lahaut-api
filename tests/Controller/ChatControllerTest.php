<?php

namespace App\Tests\Controller;

use App\Entity\Chat\Conversation;
use App\Entity\User;
use App\Repository\Chat\ConversationRepository;
use App\Repository\UserRepository;
use App\Service\ChatService;
use App\Tests\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ChatControllerTest extends ApiTestCase
{
    public function testShouldHaveNoConversationsWithEmptyDataset(): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $repository */
        $repository = $manager->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');
        $this->loginApiUser($user);

        $this->apiGet('/conversations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals([], $response, 'conversation found on empty database (did you reset the database?)');
    }

    public function testShouldHaveConversationsBetweenCustomerAndAdmin(): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $repository */
        $repository = $manager->getRepository(User::class);

        /** @var User $customer */
        $customer = $repository->findOneByEmail('customer@mail.com');

        /** @var User $admin */
        $admin = $repository->findOneByEmail('admin@mail.com');

        /** @var ChatService $chatService */
        $chatService = self::getContainer()->get(ChatService::class);

        $conversation = $chatService->createNewConversationBetween([$customer, $admin]);

        $manager->persist($conversation);
        $manager->flush();

        // Test for admin as current user
        $this->loginApiUser($admin);
        $this->apiGet('/conversations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $this->getApiResponse(), 'conversation not found');

        // Test for customer as current user
        $this->loginApiUser($customer);
        $this->apiGet('/conversations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $this->getApiResponse(), 'conversation not found');
    }
}
