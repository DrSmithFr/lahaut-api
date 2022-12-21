<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Enum\UserEnum;
use App\Model\RegisterModel;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegisterControllerTest extends ApiTestCase
{
    public function testRegisterUserWithBadEmail(): void
    {
        $form = (new RegisterModel())
            ->setUsername('not_an_email')
            ->setPassword('password');

        $this->post('/register/user', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterUserWithShortPassword(): void
    {
        $form = (new RegisterModel())
            ->setUsername('test@gmail.com')
            ->setPassword('...');

        $this->post('/register/user', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterUserEmailAlreadyUsed(): void
    {
        $form = (new RegisterModel())
            ->setUsername('user@gmail.com')
            ->setPassword('password');

        $this->post('/register/user', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterUserValid(): void
    {
        $form = (new RegisterModel())
            ->setUsername('test-user@gmail.com')
            ->setPassword('password');

        $this->post('/register/user', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('test-user@gmail.com');

        $this->assertEquals([UserEnum::USER->getRole()], $user->getRoles());
    }

    public function testRegisterMonitorValid(): void
    {
        $form = (new RegisterModel())
            ->setUsername('test-monitor@gmail.com')
            ->setPassword('password');

        $this->post('/register/monitor', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('test-monitor@gmail.com');

        $this->assertEquals([UserEnum::MONITOR->getRole()], $user->getRoles());
    }
}
