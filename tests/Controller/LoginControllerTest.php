<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends ApiTestCase
{
    public function testLoginBadUuid(): void
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
            Response::HTTP_INTERNAL_SERVER_ERROR,
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
            '{"username":"766af668-0c19-4624-bcb4-bdb09ce4dada","password":"badpassword"}'
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
            '{"username":"766af668-0c19-4624-bcb4-bdb09ce4dada","password":"admin-passwd"}'
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
            '{"username":"0b819649-bef4-4fb9-a6b4-7b7b0b69961c","password":"user-passwd"}'
        );

        $this->assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode(),
            'cannot connect with user credential'
        );
    }
}
