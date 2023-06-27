<?php

namespace App\Tests\Controller;

use App\DataFixtures\ActivityTypeFixtures;
use App\DataFixtures\SlotFixtures;
use App\Entity\User;
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

        $this->apiGet('/public/slots/' . ActivityTypeFixtures::REFERENCE_DISCOVERY . '/' . $today);
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

        $this->apiGet('/public/slots/' . ActivityTypeFixtures::REFERENCE_FREESTYLE . '/' . $today);
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

        $this->apiGet(
            '/public/slots/' . $monitor->getUuid() . '/' . ActivityTypeFixtures::REFERENCE_DISCOVERY . '/' . $today
        );

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

        $this->apiGet(
            '/public/slots/' . $monitor->getUuid() . '/' . ActivityTypeFixtures::REFERENCE_FREESTYLE . '/' . $today
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            count(SlotFixtures::SLOTS_FREESTYLE),
            count($response),
            'number of slots in database dont match fixture (did you reset the database?)'
        );
    }
}
