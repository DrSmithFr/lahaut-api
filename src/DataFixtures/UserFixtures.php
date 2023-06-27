<?php

namespace App\DataFixtures;

use App\Enum\RoleEnum;
use App\Service\User\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    final public const REFERENCE_ADMIN = 'user-admin';
    final public const REFERENCE_USER = 'user-customer';
    final public const REFERENCE_MONITOR = 'user-monitor';
    final public const REFERENCE_DISABLED = 'user-disabled';

    public function __construct(private readonly UserService $userService)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = $this->userService->createUser('admin@mail.com', 'admin-password');
        $user = $this->userService->createUser('customer@mail.com', 'customer-password');
        $monitor = $this->userService->createUser('monitor@mail.com', 'monitor-password');
        $disabled = $this->userService->createUser('disable@mail.com', 'disable-password');

        $this->setReference(self::REFERENCE_ADMIN, $admin);
        $this->setReference(self::REFERENCE_USER, $user);
        $this->setReference(self::REFERENCE_MONITOR, $monitor);
        $this->setReference(self::REFERENCE_DISABLED, $disabled);

        $admin->setEnable(true);
        $user->setEnable(true);
        $monitor->setEnable(true);
        $disabled->setEnable(false);

        $admin->addRole(RoleEnum::ADMIN);
        $user->addRole(RoleEnum::CUSTOMER);
        $monitor->addRole(RoleEnum::MONITOR);
        $disabled->addRole(RoleEnum::CUSTOMER);

        $manager->persist($admin);
        $manager->persist($user);
        $manager->persist($monitor);
        $manager->persist($disabled);

        $manager->flush();
    }
}
