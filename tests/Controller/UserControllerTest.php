<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Model\PasswordUpdateModel;
use App\Model\RegisterModel;
use App\Service\UserService;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends ApiTestCase
{
    public function testPasswordUpdateUnconnected(): void
    {
        $form = (new PasswordUpdateModel())
            ->setCurrentPassword('customer-password')
            ->setNewPassword('new-password');

        $this->apiPatch('/user/password_update', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testPasswordUpdateWithBadCurrentPassword()
    {
        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        // simulate user connection
        $this->loginApiUser($user);

        $form = (new PasswordUpdateModel())
            ->setCurrentPassword('bad-current-password')
            ->setNewPassword('new-password');

        $this->apiPatch('/user/password_update', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        // remove simulate user connection
        $this->disconnectUser();
    }

    public function testPasswordUpdateWithTooSmallNewPassword(): void
    {
        $form = (new RegisterModel())
            ->setUsername('password-update-too-short@mail.com')
            ->setPassword('password');

        $this->apiPost('/register/customer', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('password-update-too-short@mail.com');

        // simulate user connection
        $this->loginApiUser($user);

        $form = (new PasswordUpdateModel())
            ->setCurrentPassword('password')
            ->setNewPassword('...');

        $this->apiPatch('/user/password_update', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        // remove simulate user connection
        $this->disconnectUser();
    }

    public function testPasswordUpdateValid(): void
    {
        $form = (new RegisterModel())
            ->setUsername('update-password-valid@mail.com')
            ->setPassword('password');

        $this->apiPost('/register/customer', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('update-password-valid@mail.com');

        // simulate user connection
        $this->loginApiUser($user);

        $form = (new PasswordUpdateModel())
            ->setCurrentPassword('password')
            ->setNewPassword('new-password');

        $this->apiPatch('/user/password_update', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        /** @var User $user */
        $user = $repository->findOneByEmail('update-password-valid@mail.com');
        $userService = self::getContainer()->get(UserService::class);

        // test if password has been changed
        $this->assertTrue(
            $userService->isPasswordValid($user, 'new-password'),
            'Password has not been changed'
        );

        // remove simulate user connection
        $this->disconnectUser();
    }

    public function testUpdateIdentity()
    {
        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        // simulate user connection
        $this->loginApiUser($user);

        $data = [
            "anniversary" => "1992-10-06",
            "firstName" => "bob",
            "lastName" => "moran",
            "nationality" => "fr",
            "phone" => "+33612345678"
        ];

        $this->apiPut('/user/identity', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertEquals($data, $this->getApiResponse(), 'Identity has not been updated');
    }

    public function testUpdateAddress()
    {
        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        // simulate user connection
        $this->loginApiUser($user);

        $data = [
            "street" => "1 road street",
            "zipCode" => "69000",
            "city" => "Lyon",
            "country" => "FR"
        ];

        $this->apiPut('/user/address', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertEquals($data, $this->getApiResponse(), 'Address has not been updated');

        $this->apiPut('/user/billing_address', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertEquals($data, $this->getApiResponse(), 'Address has not been updated');
    }
}
