<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Fly\Place\MeetingPoint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MeetingPointFixtures extends Fixture
{
    public const IDENTIFIER = 'meeting-point';
    public function load(ObjectManager $manager): void
    {
        $meetingPoint = (new MeetingPoint())
            ->setIdentifier(self::IDENTIFIER)
            ->setName('Meeting Point')
            ->setAddress(
                (new Address())
                    ->setStreet('Rue de la République')
                    ->setCity('Paris')
                    ->setZipCode('75001')
                    ->setCountry('France')
            )
            ->setLatitude(0)
            ->setLongitude(0);

        $this->setReference(self::IDENTIFIER, $meetingPoint);

        $manager->persist($meetingPoint);
        $manager->flush();
    }
}
