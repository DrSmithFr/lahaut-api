<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Activity\Place\MeetingPoint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MeetingPointFixtures extends Fixture
{
    public const ORM_IDENTIFIER = 'meeting-point-from-fixture';
    public const REFERENCE = 'meeting-point';

    public function load(ObjectManager $manager): void
    {
        $meetingPoint = (new MeetingPoint())
            ->setIdentifier(self::ORM_IDENTIFIER)
            ->setName('Meeting Point')
            ->setAddress(
                (new Address())
                    ->setStreet('Rue de la République')
                    ->setCity('Paris')
                    ->setZipCode('75001')
                    ->setCountry('France')
            )
            ->setLatitude("45.9399268")
            ->setLongitude("6.885664");

        $this->setReference(self::REFERENCE, $meetingPoint);

        $manager->persist($meetingPoint);
        $manager->flush();
    }
}
