<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegisterControllerTest extends ApiTestCase
{
    public function testRegisterUserWithBadEmail(): void
    {
        $this->apiPost(
            '/public/register/customer',
            [
                'username' => 'not_an_email',
                'password' => 'password',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterUserWithShortPassword(): void
    {
        $this->apiPost(
            '/public/register/customer',
            [
                'username' => 'test-short-password@mail.com',
                'password' => '...',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterUserEmailAlreadyUsed(): void
    {
        $this->apiPost(
            '/public/register/customer',
            [
                'username' => 'customer@mail.com',
                'password' => 'password',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterCustomerValid(): void
    {
        $this->apiPost(
            '/public/register/customer',
            [
                'username' => 'test-customer@mail.com',
                'password' => 'password',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('test-customer@mail.com');

        $this->assertEquals([RoleEnum::CUSTOMER->value], $user->getRoles());
    }

    public function testRegisterMonitorValid(): void
    {
        $this->apiPost(
            '/public/register/monitor',
            [
                'username'  => 'test-monitor@mail.com',
                'password'  => 'password',
                'firstname' => 'John',
                'lastname'  => 'Doe',
                'phone'     => '+33612345678',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('test-monitor@mail.com');

        $this->assertEquals([RoleEnum::MONITOR->value], $user->getRoles());
    }
}
