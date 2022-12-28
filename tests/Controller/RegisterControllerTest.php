<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Enum\RoleEnum;
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

        $this->apiPost('/register/customer', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterUserWithShortPassword(): void
    {
        $form = (new RegisterModel())
            ->setUsername('test-short-password@mail.com')
            ->setPassword('...');

        $this->apiPost('/register/customer', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterUserEmailAlreadyUsed(): void
    {
        $form = (new RegisterModel())
            ->setUsername('customer@mail.com')
            ->setPassword('password');

        $this->apiPost('/register/customer', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterCustomerValid(): void
    {
        $form = (new RegisterModel())
            ->setUsername('test-customer@mail.com')
            ->setPassword('password');

        $this->apiPost('/register/customer', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('test-customer@mail.com');

        $this->assertEquals([RoleEnum::CUSTOMER->getRole()], $user->getRoles());
    }

    public function testRegisterMonitorValid(): void
    {
        $form = (new RegisterModel())
            ->setUsername('test-monitor@mail.com')
            ->setPassword('password');

        $this->apiPost('/register/monitor', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('test-monitor@mail.com');

        $this->assertEquals([RoleEnum::MONITOR->getRole()], $user->getRoles());
    }
}
