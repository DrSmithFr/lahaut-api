<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use App\Tests\ApiTestCase;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordControllerTest extends ApiTestCase
{
    public function testRequestPasswordResetWithUnknownUser(): void
    {
        $this->apiPost(
            '/auth/reset_password',
            [
                'username' => 'unknown@mail.com'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testRequestPasswordResetWithNewPasswordTooShort(): void
    {
        $this->apiPost(
            '/auth/reset_password',
            [
                'username' => 'customer@mail.com'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        $this->assertNotNull($user->getPasswordResetToken());

        $this->apiPatch(
            '/auth/reset_password',
            [
                'token'    => $user->getPasswordResetToken(),
                'password' => '...'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testRequestPasswordResetWithBadToken(): void
    {
        $this->apiPost(
            '/auth/reset_password',
            [
                'username' => 'customer@mail.com'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        $this->assertNotNull($user->getPasswordResetToken(), 'Password reset token is null');

        $this->apiPatch(
            '/auth/reset_password',
            [
                'token'    => 'bad_token',
                'password' => 'new_password'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testRequestPasswordResetWithExpireToken(): void
    {
        $this->apiPost(
            '/auth/reset_password',
            [
                'username' => 'customer@mail.com'
            ]
        );

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

        $this->apiPatch(
            '/auth/reset_password',
            [
                'token'    => $user->getPasswordResetToken(),
                'password' => 'new_password'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_ACCEPTABLE);
    }

    public function testRequestPasswordResetValid(): void
    {
        $this->apiPost(
            '/auth/reset_password',
            [
                'username' => 'customer@mail.com'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $repository */
        $repository = $manager->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        $this->assertNotNull($user->getPasswordResetToken(), 'Password reset token is null');

        $this->apiPatch(
            '/auth/reset_password',
            [
                'token'    => $user->getPasswordResetToken(),
                'password' => 'new-password'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');
        $userService = self::getContainer()->get(UserService::class);

        $this->assertTrue(
            $userService->isPasswordValid($user, 'new-password'),
            'Password has not been changed'
        );

        $this->assertNull(
            $user->getPasswordResetToken(),
            'Password reset token has not been removed'
        );

        $this->assertNull(
            $user->getPasswordResetTokenValidUntil(),
            'Password reset token validity until has not been removed'
        );

        // rollback to the original password
        $user->setPlainPassword('customer-password');
        $userService->updatePassword($user);

        // ensure rollback can be saved to database
        $this->assertTrue($manager->contains($user), 'User Entity is not managed');

        $manager->flush();
    }

    public function testIsPasswordResetTokenValidWithBadToken(): void
    {
        $this->apiPost(
            '/auth/reset_password/validity',
            [
                'token' => 'bad_token'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testIsPasswordResetTokenValid(): void
    {
        $this->apiPost(
            '/auth/reset_password',
            [
                'username' => 'customer@mail.com'
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $repository = self::getContainer()
                          ->get('doctrine')
                          ->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');

        $this->apiPost(
            '/auth/reset_password/validity',
            [
                'token' => $user->getPasswordResetToken()
            ]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }
}
