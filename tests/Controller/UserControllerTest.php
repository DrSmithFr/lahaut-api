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

        $this->patch('/user/password_update', $form);

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
        $this->loginUser($user);

        $form = (new PasswordUpdateModel())
            ->setCurrentPassword('bad-current-password')
            ->setNewPassword('new-password');

        $this->patch('/user/password_update', $form);

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

        $this->post('/register/customer', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('password-update-too-short@mail.com');

        // simulate user connection
        $this->loginUser($user);

        $form = (new PasswordUpdateModel())
            ->setCurrentPassword('password')
            ->setNewPassword('...');

        $this->patch('/user/password_update', $form);

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

        $this->post('/register/customer', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('update-password-valid@mail.com');

        // simulate user connection
        $this->loginUser($user);

        $form = (new PasswordUpdateModel())
            ->setCurrentPassword('password')
            ->setNewPassword('new-password');

        $this->patch('/user/password_update', $form);

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
}
