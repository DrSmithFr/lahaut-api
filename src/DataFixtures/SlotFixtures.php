<?php

namespace App\DataFixtures;

use App\Entity\Fly\Place\LandingPoint;
use App\Entity\Fly\Place\MeetingPoint;
use App\Entity\Fly\Place\TakeOffPoint;
use App\Entity\Fly\Slot;
use App\Entity\User;
use App\Enum\FlyTypeEnum;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SlotFixtures extends Fixture implements DependentFixtureInterface
{
    public const SLOTS = [
        ['09:00:00', '10:00:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['10:00:00', '11:00:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['11:00:00', '12:00:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['14:00:00', '15:00:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['15:00:00', '16:00:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['16:00:00', '17:00:00', 'PT20M', FlyTypeEnum::DISCOVERY],
        ['17:00:00', '18:00:00', 'PT20M', FlyTypeEnum::DISCOVERY],

        ['09:00:00', '11:00:00', 'PT40M', FlyTypeEnum::FREESTYLE],
        ['10:00:00', '12:00:00', 'PT40M', FlyTypeEnum::FREESTYLE],
        ['14:00:00', '16:00:00', 'PT40M', FlyTypeEnum::FREESTYLE],
        ['15:00:00', '17:00:00', 'PT40M', FlyTypeEnum::FREESTYLE],
        ['16:00:00', '18:00:00', 'PT40M', FlyTypeEnum::FREESTYLE],
    ];

    public function load(ObjectManager $manager): void
    {
        /** @var MeetingPoint $meetingPoint */
        $meetingPoint = $this->getReference(MeetingPointFixtures::REFERENCE);

        /** @var LandingPoint $landingPoint */
        $landingPoint = $this->getReference(LandingPointFixtures::REFERENCE);

        /** @var TakeOffPoint $takeOffPoint */
        $takeOffPoint = $this->getReference(TakeOffPointFixtures::REFERENCE);

        /** @var User $monitor */
        $monitor = $this->getReference(UserFixtures::REFERENCE_MONITOR, User::class);

        $now = (new DateTime())->format('Y-m-d'); // enforce standardization

        foreach (self::SLOTS as $slot) {
            $startAt = new DateTimeImmutable(sprintf('%s %s', $now, $slot[0]));
            $endAt = new DateTimeImmutable(sprintf('%s %s', $now, $slot[1]));

            $slot = (new Slot())
                ->setMeetingPoint($meetingPoint)
                ->setLandingPoint($landingPoint)
                ->setTakeOffPoint($takeOffPoint)
                ->setStartAt($startAt)
                ->setEndAt($endAt)
                ->setAverageFlyDuration(new DateInterval($slot[2]))
                ->setType($slot[3])
                ->setMonitor($monitor);

            $manager->persist($slot);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            MeetingPointFixtures::class,
            LandingPointFixtures::class,
            TakeOffPointFixtures::class,
        ];
    }
}
