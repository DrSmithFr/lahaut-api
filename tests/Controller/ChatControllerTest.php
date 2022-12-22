<?php

namespace App\Tests\Controller;

use App\Model\LoginModel;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ChatControllerTest extends ApiTestCase
{
    public function testCreateConversation(): void
    {
        $form = (new LoginModel())
            ->setUsername('bad_user')
            ->setPassword('bad_password');

        $this->post('/login', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
