<?php

namespace App\Tests\Controller;

use App\Model\RegisterModel;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegisterControllerTest extends ApiTestCase
{
    public function testRegisterWithBadEmail(): void
    {
        $form = (new RegisterModel())
            ->setUsername('not_an_email')
            ->setPassword('password');

        $this->post('/register', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterWithShortPassword(): void
    {
        $form = (new RegisterModel())
            ->setUsername('test@gmail.com')
            ->setPassword('...');

        $this->post('/register', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterValid(): void
    {
        $form = (new RegisterModel())
            ->setUsername('test@gmail.com')
            ->setPassword('password');

        $this->post('/register', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRegisterEmailAlreadyUsed(): void
    {
        $form = (new RegisterModel())
            ->setUsername('user@gmail.com')
            ->setPassword('password');

        $this->post('/register', $form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }
}
