<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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
}
