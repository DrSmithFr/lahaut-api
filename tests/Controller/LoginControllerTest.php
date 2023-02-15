<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends ApiTestCase
{
    public function testLoginWithUserNotFound(): void
    {
        $this->apiPost(
            '/login',
            [
                'username' => 'bad_user',
                'password' => 'bad_password',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginBadCredencial(): void
    {
        $this->apiPost(
            '/login',
            [
                'username' => 'admin@mail.com',
                'password' => 'bad_password',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLoginAdmin(): void
    {
        $this->apiPost(
            '/login',
            [
                'username' => 'admin@mail.com',
                'password' => 'admin-password',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginCustomer(): void
    {
        $this->apiPost(
            '/login',
            [
                'username' => 'customer@mail.com',
                'password' => 'customer-password',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginUserDisable(): void
    {
        $this->apiPost(
            '/login',
            [
                'username' => 'disable@mail.com',
                'password' => 'disable-password',
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
