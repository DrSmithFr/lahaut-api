<?php

namespace App\DataFixtures;

use App\Entity\Activity\ActivityLocation;
use App\Entity\Activity\Place\LandingPoint;
use App\Entity\Activity\Place\MeetingPoint;
use App\Entity\Activity\Place\TakeOffPoint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActivityLocationFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE = 'chamonix-default';

    public function load(ObjectManager $manager): void
    {
        /** @var TakeOffPoint $takeOffPoint */
        $takeOffPoint = $this->getReference(TakeOffPointFixtures::REFERENCE);

        /** @var MeetingPoint $meetingPoint */
        $meetingPoint = $this->getReference(MeetingPointFixtures::REFERENCE);

        /** @var LandingPoint $landingPoint */
        $landingPoint = $this->getReference(LandingPointFixtures::REFERENCE);

        $activityLocation = (new ActivityLocation())
            ->setIdentifier(self::REFERENCE)
            ->setName('Chamonix')
            ->setTakeOffPoint($takeOffPoint)
            ->setMeetingPoint($meetingPoint)
            ->setLandingPoint($landingPoint);

        $this->setReference(self::REFERENCE, $activityLocation);

        $manager->persist($activityLocation);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TakeOffPointFixtures::class,
            MeetingPointFixtures::class,
            LandingPointFixtures::class,
        ];
    }
}
