<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Fly\Place\LandingPoint;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LandingPointFixtures extends Fixture
{
    public const ORM_IDENTIFIER = 'landing-point-from-fixture';
    public const REFERENCE = 'landing-point';

    public function load(ObjectManager $manager): void
    {
        $landingPoint = (new LandingPoint())
            ->setIdentifier(self::ORM_IDENTIFIER)
            ->setName('Landing Point')
            ->setAddress(
                (new Address())
                    ->setStreet('Rue de la République')
                    ->setCity('Paris')
                    ->setZipCode('75001')
                    ->setCountry('France')
            )
            ->setLatitude("46.023576")
            ->setLongitude("6.84999928");

        $this->setReference(self::REFERENCE, $landingPoint);

        $manager->persist($landingPoint);
        $manager->flush();
    }
}
