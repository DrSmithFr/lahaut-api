<?php

namespace App\Tests\Controller;

use App\Model\LoginModel;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends ApiTestCase
{
    public function testLoginWithUserNotFound(): void
    {
        $form = (new LoginModel())
            ->setUsername('bad_user')
            ->setPassword('bad_password');

        $this->post('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginBadCredencial(): void
    {
        $form = (new LoginModel())
            ->setUsername('admin@gmail.com')
            ->setPassword('bad_password');

        $this->post('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginAdmin(): void
    {
        $form = (new LoginModel())
            ->setUsername('admin@gmail.com')
            ->setPassword('admin-password');

        $this->post('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginUser(): void
    {
        $form = (new LoginModel())
            ->setUsername('user@gmail.com')
            ->setPassword('user-password');

        $this->post('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginUserDisable(): void
    {
        $form = (new LoginModel())
            ->setUsername('disable@gmail.com')
            ->setPassword('disable-password');

        $this->post('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
