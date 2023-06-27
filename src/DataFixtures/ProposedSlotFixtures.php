<?php

namespace App\DataFixtures;

use App\Entity\Activity\ActivityType;
use App\Entity\Slot\SlotProposed;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProposedSlotFixtures extends Fixture implements DependentFixtureInterface
{
    public const SLOTS_DISCOVERY = [
        ['08:00', '09:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
        ['09:00', '10:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
        ['10:00', '11:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
        ['11:00', '12:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
        ['12:00', '13:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
        ['13:00', '14:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
        ['14:00', '15:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
        ['15:00', '16:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
        ['16:00', '17:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
        ['17:00', '18:00', 'PT20M', ActivityTypeFixtures::REFERENCE_DISCOVERY],
    ];

    public const SLOTS_FREESTYLE = [
        ['08:00', '09:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
        ['09:00', '10:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
        ['10:00', '11:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
        ['11:00', '12:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
        ['12:00', '13:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
        ['13:00', '14:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
        ['14:00', '15:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
        ['15:00', '16:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
        ['16:00', '17:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
        ['17:00', '18:00', 'PT20M', ActivityTypeFixtures::REFERENCE_FREESTYLE],
    ];

    public const SLOTS_XL = [
        ['08:00', '10:00', 'PT40M', ActivityTypeFixtures::REFERENCE_XL],
        ['09:00', '11:00', 'PT40M', ActivityTypeFixtures::REFERENCE_XL],
        ['10:00', '12:00', 'PT40M', ActivityTypeFixtures::REFERENCE_XL],
        ['11:00', '13:00', 'PT40M', ActivityTypeFixtures::REFERENCE_XL],
        ['12:00', '14:00', 'PT40M', ActivityTypeFixtures::REFERENCE_XL],
        ['13:00', '15:00', 'PT40M', ActivityTypeFixtures::REFERENCE_XL],
        ['14:00', '16:00', 'PT40M', ActivityTypeFixtures::REFERENCE_XL],
        ['15:00', '17:00', 'PT40M', ActivityTypeFixtures::REFERENCE_XL],
        ['16:00', '18:00', 'PT40M', ActivityTypeFixtures::REFERENCE_XL],
    ];

    public const SLOTS = [...self::SLOTS_DISCOVERY, ...self::SLOTS_FREESTYLE, ...self::SLOTS_XL];

    public function load(ObjectManager $manager): void
    {
        foreach (self::SLOTS as $slot) {
            /** @var ActivityType $activityType */
            $activityType = $this->getReference($slot[3]);

            $slot = (new SlotProposed())
                ->setActivityType($activityType)
                ->setStartAt(DateTimeImmutable::createFromFormat('H:i', $slot[0]))
                ->setEndAt(DateTimeImmutable::createFromFormat('H:i', $slot[1]))
                ->setAverageActivityDuration(new DateInterval($slot[2]));

            $manager->persist($slot);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ActivityTypeFixtures::class,
        ];
    }
}
