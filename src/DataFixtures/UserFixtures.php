<?php

namespace App\DataFixtures;

use App\Enum\UserEnum;
use App\Service\UserService;
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

        $admin->addRole(UserEnum::ADMIN->getRole());
        $user->addRole(UserEnum::CUSTOMER->getRole());
        $monitor->addRole(UserEnum::MONITOR->getRole());
        $disabled->addRole(UserEnum::CUSTOMER->getRole());

        $manager->persist($admin);
        $manager->persist($user);
        $manager->persist($monitor);
        $manager->persist($disabled);

        $manager->flush();
    }
}
