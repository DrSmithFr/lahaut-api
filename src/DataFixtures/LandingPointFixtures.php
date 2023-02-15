<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Fly\Place\LandingPoint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LandingPointFixtures extends Fixture
{
    public const IDENTIFIER = 'landing-point';
    public function load(ObjectManager $manager): void
    {
        $landingPoint = (new LandingPoint())
            ->setIdentifier(self::IDENTIFIER)
            ->setName('Landing Point')
            ->setAddress(
                (new Address())
                    ->setStreet('Rue de la République')
                    ->setCity('Paris')
                    ->setZipCode('75001')
                    ->setCountry('France')
            )
            ->setLatitude(0)
            ->setLongitude(0);

        $this->setReference(self::IDENTIFIER, $landingPoint);

        $manager->persist($landingPoint);
        $manager->flush();
    }
}
