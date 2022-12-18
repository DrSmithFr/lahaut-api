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
    final public const REFERENCE_MONITOR  = 'user-monitor';
    final public const REFERENCE_DISABLED  = 'user-disabled';

    public function __construct(private readonly UserService $userService)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = $this->userService->createUser('admin-passwd', 'admin@gmail.com');
        $user  = $this->userService->createUser('user-passwd', 'user@gmail.com');
        $monitor  = $this->userService->createUser('monitor-passwd', 'monitor@gmail.com');
        $disabled  = $this->userService->createUser('disabled-passwd', 'disabled@gmail.com');

        $this->setReference(self::REFERENCE_ADMIN, $admin);
        $this->setReference(self::REFERENCE_USER, $user);
        $this->setReference(self::REFERENCE_MONITOR, $monitor);
        $this->setReference(self::REFERENCE_DISABLED, $disabled);

        $admin->setEnable(true);
        $user->setEnable(true);
        $monitor->setEnable(true);
        $disabled->setEnable(false);

        $admin->addRole(SecurityRoleEnum::ADMIN->getRole());
        $user->addRole(SecurityRoleEnum::USER->getRole());
        $monitor->addRole(SecurityRoleEnum::MONITOR->getRole());
        $disabled->addRole(SecurityRoleEnum::USER->getRole());

        $manager->persist($admin);
        $manager->persist($user);
        $manager->persist($monitor);
        $manager->persist($disabled);

        $manager->flush();
    }
}
