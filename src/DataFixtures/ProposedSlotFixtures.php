<?php

namespace App\DataFixtures;

use App\Entity\Fly\FlyLocation;
use App\Entity\Fly\SlotProposed;
use App\Enum\FlyTypeEnum;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProposedSlotFixtures extends Fixture implements DependentFixtureInterface
{
    public const SLOTS_DISCOVERY = [
        ['08:00', '09:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['09:00', '10:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['10:00', '11:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['11:00', '12:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['12:00', '13:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['13:00', '14:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['14:00', '15:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['15:00', '16:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['16:00', '17:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['17:00', '18:00', 'PT20M', FlyTypeEnum::DISCOVERY],
    ];

    public const SLOTS_FREESTYLE = [
        ['08:00', '09:00', 'PT20M', FlyTypeEnum::FREESTYLE],
        ['09:00', '10:00', 'PT20M', FlyTypeEnum::FREESTYLE],
        ['10:00', '11:00', 'PT20M', FlyTypeEnum::FREESTYLE],
        ['11:00', '12:00', 'PT20M', FlyTypeEnum::FREESTYLE],
        ['12:00', '13:00', 'PT20M', FlyTypeEnum::FREESTYLE],
        ['13:00', '14:00', 'PT20M', FlyTypeEnum::FREESTYLE],
        ['14:00', '15:00', 'PT20M', FlyTypeEnum::FREESTYLE],
        ['15:00', '16:00', 'PT20M', FlyTypeEnum::FREESTYLE],
        ['16:00', '17:00', 'PT20M', FlyTypeEnum::FREESTYLE],
        ['17:00', '18:00', 'PT20M', FlyTypeEnum::FREESTYLE],
    ];

    public const SLOTS_XL = [
        ['08:00', '10:00', 'PT40M', FlyTypeEnum::XL],
        ['09:00', '11:00', 'PT40M', FlyTypeEnum::XL],
        ['10:00', '12:00', 'PT40M', FlyTypeEnum::XL],
        ['11:00', '13:00', 'PT40M', FlyTypeEnum::XL],
        ['12:00', '14:00', 'PT40M', FlyTypeEnum::XL],
        ['13:00', '15:00', 'PT40M', FlyTypeEnum::XL],
        ['14:00', '16:00', 'PT40M', FlyTypeEnum::XL],
        ['15:00', '17:00', 'PT40M', FlyTypeEnum::XL],
        ['16:00', '18:00', 'PT40M', FlyTypeEnum::XL],
    ];

    public const SLOTS = [...self::SLOTS_DISCOVERY, ...self::SLOTS_FREESTYLE, ...self::SLOTS_XL];

    public function load(ObjectManager $manager): void
    {
        /** @var FlyLocation $flyLocation */
        $flyLocation = $this->getReference(FlyLocationFixtures::REFERENCE);

        foreach (self::SLOTS as $slot) {
            $slot = (new SlotProposed())
                ->setFlyLocation($flyLocation)
                ->setStartAt(DateTimeImmutable::createFromFormat('H:i', $slot[0]))
                ->setEndAt(DateTimeImmutable::createFromFormat('H:i', $slot[1]))
                ->setAverageFlyDuration(new DateInterval($slot[2]))
                ->setType($slot[3]);

            $manager->persist($slot);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FlyLocationFixtures::class,
        ];
    }
}
