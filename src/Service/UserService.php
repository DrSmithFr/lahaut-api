<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserService
{
    private UserPasswordHasherInterface $passwordHasher;
    private TokenGeneratorInterface $tokenGenerator;
    private MailerService $mailerService;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        TokenGeneratorInterface     $tokenGenerator,
        MailerService               $mailerService
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailerService  = $mailerService;
    }

    /**
     * @param string $password
     * @param string $email
     * @return User
     */
    public function createUser(string $password, string $email): User
    {
        $user = (new User())
            ->setEmail(strtolower($email))
            ->setPlainPassword($password);

        $this->updatePassword($user);

        return $user;
    }

    public function updatePassword(User $user): User
    {
        $encoded = $this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        );

        $user->setPassword($encoded);
        $user->setPlainPassword(null);

        return $user;
    }

    public function generateResetToken(User $user): User
    {
        $user->setPasswordResetToken($this->tokenGenerator->generateToken());
        $user->setPasswordResetAt(new DateTime());

        $this->mailerService->sendPasswordResetEmail($user);

        return $user;
    }
}
