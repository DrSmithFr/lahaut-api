<?php


namespace App\DataFixtures;

use Exception;
use Ramsey\Uuid\Uuid;
use App\Service\UserService;
use App\Enum\SecurityRoleEnum;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    final public const REFERENCE_ADMIN = 'user-admin';
    final public const REFERENCE_USER  = 'user-user';

    public function __construct(private readonly UserService $userService)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = $this->userService->createUser('admin-passwd', 'admin@gmail.com');
        $user  = $this->userService->createUser('user-passwd', 'user@gmail.com');

        $this->setReference(self::REFERENCE_ADMIN, $admin);
        $this->setReference(self::REFERENCE_USER, $user);

        $admin->addRole(SecurityRoleEnum::ADMIN);
        $user->addRole(SecurityRoleEnum::USER);

        $manager->persist($admin);
        $manager->persist($user);

        $manager->flush();
    }
}
