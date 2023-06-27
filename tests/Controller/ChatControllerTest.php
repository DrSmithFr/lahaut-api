<?php

namespace App\Tests\Controller;

use App\Entity\_Chat\Conversation;
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

        // Test data integrity
        $data = $this->getApiResponse()[0];
        $this->assertEquals($conversation->getUuid(), $data['uuid'], 'conversation id mismatch');
        $this->assertEquals((string)$customer->getUuid(), $data['participants'][0]['uuid'], 'customer id mismatch');
        $this->assertEquals((string)$admin->getUuid(), $data['participants'][1]['uuid'], 'admin id mismatch');
    }

    /**
     * @depends testShouldHaveConversationsBetweenCustomerAndAdmin
     */
    public function testAddMessage(): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var ConversationRepository $conversationRepository */
        $conversationRepository = $manager->getRepository(Conversation::class);

        /** @var User $customer */
        $customer = $userRepository->findOneByEmail('customer@mail.com');

        /** @var User $admin */
        $admin = $userRepository->findOneByEmail('admin@mail.com');

        /** @var ChatService $chatService */
        $conversation = $conversationRepository->getOneByParticipants([$customer, $admin]);

        $this->loginApiUser($customer);
        $this->apiGet('/conversations/' . $conversation->getUuid());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(0, $this->getApiResponse(), 'conversation not empty');

        $this->apiPost(
            '/conversations/' . $conversation->getUuid(),
            [
                'content' => 'Hello world',
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $this->apiGet('/conversations/' . $conversation->getUuid());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertCount(1, $this->getApiResponse(), 'conversation empty');

        $data = $this->getApiResponse()[0];
        $this->assertEquals('Hello world', $data['content'], 'message content mismatch');
    }

    public function testInitializeNewConversationWithBadForm()
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var User $customer */
        $customer = $userRepository->findOneByEmail('customer@mail.com');

        $this->loginApiUser($customer);
        $this->apiPost(
            '/conversations',
            [
                'bad_parameter' => [],
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testInitializeNewConversationWithNoData()
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var User $customer */
        $customer = $userRepository->findOneByEmail('customer@mail.com');

        $this->loginApiUser($customer);
        $this->apiPost(
            '/conversations',
            [
                'users' => []
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_ACCEPTABLE);
    }

    public function testInitializeNewConversationWithBadData()
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var User $customer */
        $customer = $userRepository->findOneByEmail('customer@mail.com');

        $this->loginApiUser($customer);
        $this->apiPost(
            '/conversations',
            [
                'users' => ['bad_uuid']
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testInitializeNewConversationWithYourself()
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var User $customer */
        $customer = $userRepository->findOneByEmail('customer@mail.com');

        $this->loginApiUser($customer);
        $this->apiPost(
            '/conversations',
            [
                'users' => [(string)$customer->getUuid()]
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testInitializeNewConversation()
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var User $customer */
        $customer = $userRepository->findOneByEmail('customer@mail.com');

        /** @var User $monitor */
        $monitor = $userRepository->findOneByEmail('monitor@mail.com');

        $this->loginApiUser($customer);
        $this->apiPost(
            '/conversations',
            [
                'users' => [(string)$monitor->getUuid()]
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $uuidConversation = $this->getApiResponse()['uuid'];

        $this->apiPost(
            '/conversations',
            [
                'users' => [(string)$monitor->getUuid()]
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertEquals($uuidConversation, $this->getApiResponse()['uuid'], 'conversation uuid mismatch');
    }
}
