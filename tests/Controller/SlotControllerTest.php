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
    public function testShouldHaveSameNumberOfSlotAsFixtureWithCleanDatabase(): void
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

        $this->apiGet('/slots/' . $today);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            count(SlotFixtures::SLOTS),
            count($response),
            'number of slots in database dont match fixture (did you reset the database?)'
        );
    }

    public function testShouldHaveSameNumberOfSlotForTheMonitorAsFixtureWithCleanDatabase(): void
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

        $this->apiGet('/slots/' . $monitor->getUuid() . '/' . $today);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            count(SlotFixtures::SLOTS),
            count($response),
            'number of slots in database dont match fixture (did you reset the database?)'
        );
    }

    public function testShouldHaveNoSlotForTheCustomerWithCleanDatabase(): void
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

        $this->apiGet('/slots/' . $user->getUuid() . '/' . $today);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            0,
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
                    ]
                ],
            ]
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // check if the slot is in the database
        $this->apiGet('/slots/' . $monitor->getUuid() . '/' . $today);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->getApiResponse();
        $this->assertEquals(
            count(SlotFixtures::SLOTS) + 1,
            count($response),
            'number of slots in database not updated (did you flush?)'
        );
    }
}
