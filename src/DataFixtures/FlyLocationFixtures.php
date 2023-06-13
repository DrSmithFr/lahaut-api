<?php

namespace App\DataFixtures;

use App\Entity\Fly\FlyLocation;
use App\Entity\Fly\Place\LandingPoint;
use App\Entity\Fly\Place\MeetingPoint;
use App\Entity\Fly\Place\TakeOffPoint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FlyLocationFixtures extends Fixture implements DependentFixtureInterface
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

        $flyLocation = (new FlyLocation())
            ->setIdentifier(self::REFERENCE)
            ->setName('Chamonix')
            ->setTakeOffPoint($takeOffPoint)
            ->setMeetingPoint($meetingPoint)
            ->setLandingPoint($landingPoint);

        $this->setReference(self::REFERENCE, $flyLocation);

        $manager->persist($flyLocation);
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
