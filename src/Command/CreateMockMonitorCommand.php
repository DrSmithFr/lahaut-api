<?php

namespace App\Command;

use App\DataFixtures\FlyLocationFixtures;
use App\DataFixtures\SlotFixtures;
use App\Entity\Fly\FlyLocation;
use App\Entity\Fly\Slot;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Service\UserService;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateMockMonitorCommand extends Command
{
    public function __construct(
        private readonly UserService $userService,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('app:monitor:mock')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'name of the monitor'
            )
            ->addArgument(
                'location',
                InputArgument::REQUIRED,
                'FlyLocation identifier'
            );
    }

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Slot mock generator');

        $name = $input->getArgument('name');
        $email = $name . '@monitor.mock';

        $errors = $this->validator->validate($email, new Email());

        if ($errors->count()) {
            $io->error($errors[0]->getMessage());
            return Command::INVALID;
        }

        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($email);

        if ($user === null) {
            $io->info('Creating monitor ' . $email);
            $user = $this->userService->createUser($email, 'passwd');
            $user->addRole(RoleEnum::MONITOR);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } elseif (!$user->hasRole(RoleEnum::MONITOR)) {
            $io->error('User ' . $email . ' is not a monitor');
            return Command::FAILURE;
        } else {
            $io->info('Editing Monitor ' . $email);
        }


        $io->table(
            [
                ['uuid', 'password'],
            ],
            [
                [$user->getEmail(), 'passwd'],
            ]
        );

        $io->success('Monitor created');

        $io->title('Generating monitor slots');
        $date = new DateTimeImmutable();

        $flyLocation = $this
            ->entityManager
            ->getRepository(FlyLocation::class)
            ->findOneByIdentifier(FlyLocationFixtures::ORM_IDENTIFIER);

        $io->progressStart(90);
        for ($day = 0; $day < 90; $day++) {
            $date = $date->modify('+1 day');
            $slots = $this->generateSlotsForDay($user, $flyLocation, $date);

            foreach ($slots as $slot) {
                $this->entityManager->persist($slot);
            }

            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $io->progressFinish();
        $io->success('Slots created for 90 days');

        return Command::SUCCESS;
    }

    /**
     * @param User              $monitor
     * @param FlyLocation       $location
     * @param DateTimeImmutable $day
     * @return Slot[]
     * @throws Exception
     */
    private function generateSlotsForDay(User $monitor, FlyLocation $location, DateTimeImmutable $day): array
    {
        $slots = [];

        foreach (SlotFixtures::SLOTS as $data) {
            $slot = (new Slot())
                ->setMonitor($monitor)
                ->setFlyLocation($location)
                ->setStartAt($day->setTime(...explode(':', $data[0])))
                ->setEndAt($day->setTime(...explode(':', $data[1])))
                ->setAverageFlyDuration(new DateInterval($data[2]))
                ->setType($data[3]);

            $slots[] = $slot;
        }

        return $slots;
    }
}
