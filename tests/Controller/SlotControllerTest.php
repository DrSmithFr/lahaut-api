<?php

namespace App\Tests\Controller;

use App\DataFixtures\FlyLocationFixtures;
use App\DataFixtures\SlotFixtures;
use App\Entity\Fly\FlyLocation;
use App\Entity\User;
use App\Enum\FlyTypeEnum;
use App\Repository\UserRepository;
use App\Tests\ApiTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class SlotControllerTest extends ApiTestCase
{
    public function testShouldHaveSameNumberOfDiscoverySlotAsFixtureWithCleanDatabase(): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $repository */
        $repository = $manager->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');
        $this->loginApiUser($user);
        $today = (new DateTimeImmutable())->format('Y-m-d');

        $this->apiGet('/public/slots/fly-location-from-fixture/discovery/' . $today);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            count(SlotFixtures::SLOTS_DISCOVERY),
            count($response),
            'number of slots in database dont match fixture (did you reset the database?)'
        );
    }

    public function testShouldHaveSameNumberOfFreestyleSlotAsFixtureWithCleanDatabase(): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $repository */
        $repository = $manager->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');
        $this->loginApiUser($user);
        $today = (new DateTimeImmutable())->format('Y-m-d');

        $this->apiGet('/public/slots/fly-location-from-fixture/freestyle/' . $today);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            count(SlotFixtures::SLOTS_FREESTYLE),
            count($response),
            'number of slots in database dont match fixture (did you reset the database?)'
        );
    }

    public function testShouldHaveSameNumberOfDiscoverySlotForTheMonitorAsFixtureWithCleanDatabase(): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $repository */
        $repository = $manager->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');
        $monitor = $repository->findOneByEmail('monitor@mail.com');

        $this->loginApiUser($user);
        $today = (new DateTimeImmutable())->format('Y-m-d');

        $this->apiGet('/public/slots/' . $monitor->getUuid() . '/fly-location-from-fixture/discovery/' . $today);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            count(SlotFixtures::SLOTS_DISCOVERY),
            count($response),
            'number of slots in database dont match fixture (did you reset the database?)'
        );
    }

    public function testShouldHaveSameNumberOfFreestyleSlotForTheMonitorAsFixtureWithCleanDatabase(): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var UserRepository $repository */
        $repository = $manager->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneByEmail('customer@mail.com');
        $monitor = $repository->findOneByEmail('monitor@mail.com');

        $this->loginApiUser($user);
        $today = (new DateTimeImmutable())->format('Y-m-d');

        $this->apiGet('/public/slots/' . $monitor->getUuid() . '/fly-location-from-fixture/freestyle/' . $today);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            count(SlotFixtures::SLOTS_FREESTYLE),
            count($response),
            'number of slots in database dont match fixture (did you reset the database?)'
        );
    }

    public function testShouldInsertNewSlotWithCorrectInput(): void
    {
        /** @var EntityManagerInterface $manager */
        $manager = self::getContainer()
                       ->get('doctrine')
                       ->getManager();

        /** @var User $monitor */
        $monitor = $manager
            ->getRepository(User::class)
            ->findOneByEmail('monitor@mail.com');

        /** @var FlyLocation $flyLocation */
        $flyLocation = $manager
            ->getRepository(FlyLocation::class)
            ->findOneByIdentifier(FlyLocationFixtures::ORM_IDENTIFIER);

        $this->loginApiUser($monitor);
        $today = (new DateTimeImmutable())->format('Y-m-d');

        $this->apiPut(
            '/slots',
            [
                'slots' => [
                    [
                        'flyLocation'        => (string)$flyLocation->getUuid(),
                        'type'               => FlyTypeEnum::DISCOVERY->value,
                        'startAt'            => sprintf('%s 10:00:00', $today),
                        'endAt'              => sprintf('%s 11:00:00', $today),
                        'averageFlyDuration' => 'PT40M',
                        'price'              => 130.00
                    ]
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // check if the slot is in the database
        $this->apiGet('/public/slots/' . $monitor->getUuid() . '/fly-location-from-fixture/discovery/' . $today);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            count(SlotFixtures::SLOTS_DISCOVERY) + 1,
            count($response),
            'number of slots in database not updated (did you flush?)'
        );
    }
}
