<?php

namespace App\DataFixtures;

use App\Entity\Fly\FlyLocation;
use App\Entity\Fly\FlyType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FlyTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_DISCOVERY = 'chamonix-default-type-discovery';
    public const REFERENCE_FREESTYLE = 'chamonix-default-type-freestyle';
    public const REFERENCE_XL = 'chamonix-default-type-xl';

    public function load(ObjectManager $manager): void
    {
        /** @var FlyLocation $flyLocation */
        $flyLocation = $this->getReference(FlyLocationFixtures::REFERENCE);

        $discovery = (new FlyType())
            ->setIdentifier(self::REFERENCE_DISCOVERY)
            ->setName('Découverte')
            ->setFlyLocation($flyLocation);

        $freestyle = (new FlyType())
            ->setIdentifier(self::REFERENCE_FREESTYLE)
            ->setName('Freestyle')
            ->setFlyLocation($flyLocation);

        $xl = (new FlyType())
            ->setIdentifier(self::REFERENCE_XL)
            ->setName('XL')
            ->setFlyLocation($flyLocation);

        $manager->persist($discovery);
        $manager->persist($freestyle);
        $manager->persist($xl);

        $this->addReference(self::REFERENCE_DISCOVERY, $discovery);
        $this->addReference(self::REFERENCE_FREESTYLE, $freestyle);
        $this->addReference(self::REFERENCE_XL, $xl);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FlyLocationFixtures::class,
        ];
    }
}
