<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends ApiTestCase
{
    public function testLoginWithUserNotFound(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"baduser","password":"badpassword"}'
        );

        $this->assertEquals(
            Response::HTTP_UNAUTHORIZED,
            $client->getResponse()->getStatusCode(),
            'can use a bad Uuid as username'
        );
    }

    public function testLoginBadCredencial(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"admin@gmail.com","password":"badpassword"}'
        );

        $this->assertEquals(
            Response::HTTP_UNAUTHORIZED,
            $client->getResponse()->getStatusCode(),
            'can connect with bad credential'
        );
    }

    public function testLoginAdmin(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"admin@gmail.com","password":"admin-passwd"}'
        );

        $this->assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode(),
            'cannot connect with user credential'
        );
    }

    public function testLoginUser(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"user@gmail.com","password":"user-passwd"}'
        );

        $this->assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode(),
            'cannot connect with user credential'
        );
    }
}
