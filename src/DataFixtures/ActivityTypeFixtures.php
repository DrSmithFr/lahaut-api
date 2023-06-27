<?php

namespace App\DataFixtures;

use App\Entity\Activity\ActivityLocation;
use App\Entity\Activity\ActivityType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActivityTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE_DISCOVERY = 'chamonix-default-type-discovery';
    public const REFERENCE_FREESTYLE = 'chamonix-default-type-freestyle';
    public const REFERENCE_XL = 'chamonix-default-type-xl';

    public function load(ObjectManager $manager): void
    {
        /** @var ActivityLocation $activityLocation */
        $activityLocation = $this->getReference(ActivityLocationFixtures::REFERENCE);

        $discovery = (new ActivityType())
            ->setIdentifier(self::REFERENCE_DISCOVERY)
            ->setName('Découverte')
            ->setActivityLocation($activityLocation);

        $freestyle = (new ActivityType())
            ->setIdentifier(self::REFERENCE_FREESTYLE)
            ->setName('Freestyle')
            ->setActivityLocation($activityLocation);

        $xl = (new ActivityType())
            ->setIdentifier(self::REFERENCE_XL)
            ->setName('XL')
            ->setActivityLocation($activityLocation);

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
            ActivityLocationFixtures::class,
        ];
    }
}
