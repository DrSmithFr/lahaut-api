<?php

namespace App\DataFixtures;

use App\Enum\UserEnum;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    final public const REFERENCE_ADMIN = 'user-admin';
    final public const REFERENCE_USER = 'user-user';
    final public const REFERENCE_MONITOR = 'user-monitor';
    final public const REFERENCE_DISABLED = 'user-disabled';

    public function __construct(private readonly UserService $userService)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = $this->userService->createUser('admin@gmail.com', 'admin-password');
        $user = $this->userService->createUser('user@gmail.com', 'user-password');
        $monitor = $this->userService->createUser('monitor@gmail.com', 'monitor-password');
        $disabled = $this->userService->createUser('disable@gmail.com', 'disable-password');

        $this->setReference(self::REFERENCE_ADMIN, $admin);
        $this->setReference(self::REFERENCE_USER, $user);
        $this->setReference(self::REFERENCE_MONITOR, $monitor);
        $this->setReference(self::REFERENCE_DISABLED, $disabled);

        $admin->setEnable(true);
        $user->setEnable(true);
        $monitor->setEnable(true);
        $disabled->setEnable(false);

        $admin->addRole(UserEnum::ADMIN->getRole());
        $user->addRole(UserEnum::USER->getRole());
        $monitor->addRole(UserEnum::MONITOR->getRole());
        $disabled->addRole(UserEnum::USER->getRole());

        $manager->persist($admin);
        $manager->persist($user);
        $manager->persist($monitor);
        $manager->persist($disabled);

        $manager->flush();
    }
}
