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

        $this->apiPost('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginBadCredencial(): void
    {
        $form = (new LoginModel())
            ->setUsername('admin@mail.com')
            ->setPassword('bad_password');

        $this->apiPost('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginAdmin(): void
    {
        $form = (new LoginModel())
            ->setUsername('admin@mail.com')
            ->setPassword('admin-password');

        $this->apiPost('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginCustomer(): void
    {
        $form = (new LoginModel())
            ->setUsername('customer@mail.com')
            ->setPassword('customer-password');

        $this->apiPost('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginUserDisable(): void
    {
        $form = (new LoginModel())
            ->setUsername('disable@mail.com')
            ->setPassword('disable-password');

        $this->apiPost('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
