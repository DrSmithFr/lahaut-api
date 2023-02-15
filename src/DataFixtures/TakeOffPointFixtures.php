<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Fly\Place\TakeOffPoint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TakeOffPointFixtures extends Fixture
{
    public const IDENTIFIER = 'take-off-point';
    public function load(ObjectManager $manager): void
    {
        $takeOffPoint = (new TakeOffPoint())
            ->setIdentifier(self::IDENTIFIER)
            ->setName('Take Off')
            ->setAddress(
                (new Address())
                    ->setStreet('Rue de la République')
                    ->setCity('Paris')
                    ->setZipCode('75001')
                    ->setCountry('France')
            )
            ->setLatitude(0)
            ->setLongitude(0);

        $this->setReference(self::IDENTIFIER, $takeOffPoint);

        $manager->persist($takeOffPoint);
        $manager->flush();
    }
}
