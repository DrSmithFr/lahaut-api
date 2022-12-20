<?php

namespace App\Tests\Controller;

use App\Model\RegisterModel;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetControllerTest extends ApiTestCase
{
    public function testRequestPasswordResetWithUnknownUser(): void
    {
        $data = ['username' => 'unknown@gmail.com'];

        $this->post('/password_reset', $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRequestPasswordResetValid(): void
    {
        $data = ['username' => 'user@gmail.com'];

        $this->post('/password_reset', $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }
}
