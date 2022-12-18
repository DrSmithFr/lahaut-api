<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

class UserService
{
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
        $encoded = $this->encodePassword($user->getPlainPassword());

        $user->setPassword($encoded);
        $user->setPlainPassword(null);

        return $user;
    }

    private function encodePassword(string $pass): string
    {
        return password_hash($pass, PASSWORD_ARGON2I);
    }
}
