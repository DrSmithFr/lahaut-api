<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\ApiTestCase;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordControllerTest extends ApiTestCase
{
    public function testRequestPasswordResetWithUnknownUser(): void
    {
        $data = ['username' => 'unknown@mail.com'];

        $this->post('/reset_password', $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRequestPasswordResetWithNewPasswordTooShort(): void
    {
        $data = ['username' => 'customer@mail.com'];

        $this->post('/reset_password', $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        $this->assertNotNull($user->getPasswordResetToken());

        $data = [
            'token' => $user->getPasswordResetToken(),
            'password' => '...'
        ];

        $this->patch('/reset_password', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testRequestPasswordResetWithBadToken(): void
    {
        $data = ['username' => 'customer@mail.com'];

        $this->post('/reset_password', $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        $this->assertNotNull($user->getPasswordResetToken());

        $data = [
            'token' => 'bad_token',
            'password' => 'new_password'
        ];

        $this->patch('/reset_password', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testRequestPasswordResetWithExpireToken(): void
    {
        $data = ['username' => 'customer@mail.com'];

        $this->post('/reset_password', $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $doctrine = self::getContainer()->get('doctrine');

        /** @var UserRepository $repository */
        $repository = $doctrine->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        // Update the token creation date to force the token to expire
        $user->setPasswordResetTokenValidUntil(new DateTime('-1 second'));
        $doctrine->getManager()->flush();

        $data = [
            'token' => $user->getPasswordResetToken(),
            'password' => 'new_password'
        ];

        $this->patch('/reset_password', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_ACCEPTABLE);
    }

    public function testRequestPasswordResetValid(): void
    {
        $data = ['username' => 'customer@mail.com'];

        $this->post('/reset_password', $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        $this->assertNotNull($user->getPasswordResetToken());

        $data = [
            'token' => $user->getPasswordResetToken(),
            'password' => 'customer-password' // reset to the original password
        ];

        $this->patch('/reset_password', $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');
        $this->assertNull($user->getPasswordResetToken());
        $this->assertNull($user->getPasswordResetTokenValidUntil());
    }

    public function testIsPasswordResetTokenValidWithBadToken(): void
    {
        $data = ['token' => 'bad_token'];

        $this->post('/reset_password/validity', $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testIsPasswordResetTokenValid(): void
    {
        $data = ['username' => 'customer@mail.com'];

        $this->post('/reset_password', $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        $this->post('/reset_password/validity', ['token' => $user->getPasswordResetToken()]);

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }
}
