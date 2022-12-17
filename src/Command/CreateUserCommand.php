<?php


namespace App\Command;


use Exception;
use App\Service\UserService;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserService $userService,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('app:user:generate');
    }

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Generating new user');

        $password = $this->generatePassword();
        $user     = $this->userService->createUser($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->table(
            [
                ['uuid', 'password'],
            ],
            [
                [$user->getUsername(), $password],
            ]
        );

        $io->comment('Connexion allowed, but it will need permission promotion to use every endpoint');
    }

    private function generatePassword(int $length = 32): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' .
                 '0123456789-=~!@#$%^&*()_+,.<>?;:[]{}';

        $password = '';
        $max      = strlen($chars) - 1;

        for ($index = 0; $index < $length; $index ++) {
            $password .= $chars[random_int(0, $max)];
        }

        return $password;
    }
}
