<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Activity\Place\TakeOffPoint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TakeOffPointFixtures extends Fixture
{
    public const ORM_IDENTIFIER = 'take-off-point-from-fixture';
    public const REFERENCE = 'take-off-point';

    public function load(ObjectManager $manager): void
    {
        $takeOffPoint = (new TakeOffPoint())
            ->setIdentifier(self::ORM_IDENTIFIER)
            ->setName('Take Off')
            ->setAddress(
                (new Address())
                    ->setStreet('Rue de la République')
                    ->setCity('Paris')
                    ->setZipCode('75001')
                    ->setCountry('France')
            )
            ->setLatitude("45.952215")
            ->setLongitude("6.856061");

        $this->setReference(self::REFERENCE, $takeOffPoint);

        $manager->persist($takeOffPoint);
        $manager->flush();
    }
}
