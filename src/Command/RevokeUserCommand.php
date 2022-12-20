<?php

namespace App\Command;

use App\Enum\SecurityRoleEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RevokeUserCommand extends Command
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('app:user:revoke')
            ->addArgument('email', InputArgument::REQUIRED, 'Email of user')
            ->addOption('user', 'u', InputOption::VALUE_NONE, 'Add role user')
            ->addOption('admin', 'a', InputOption::VALUE_NONE, 'Add role admin')
            ->addOption('super-admin', 's', InputOption::VALUE_NONE, 'Add role super-admin');
    }

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $email = $input->getArgument('email');
        $io = new SymfonyStyle($input, $output);

        $io->title('Promoting ' . $email);

        $user = $this
            ->repository
            ->findOneByEmail($email);

        if (!$user) {
            throw new UserNotFoundException('email', $email);
        }

        if ($input->getOption('user')) {
            $user->removeRole(SecurityRoleEnum::USER->getRole());
        }

        if ($input->getOption('admin')) {
            $user->removeRole(SecurityRoleEnum::ADMIN->getRole());
        }

        if ($input->getOption('super-admin')) {
            $user->removeRole(SecurityRoleEnum::SUPER_ADMIN->getRole());
        }

        $this->entityManager->flush();

        $io->success(
            sprintf(
                '%s updated with roles : %s',
                $email,
                implode(', ', $user->getRoles())
            )
        );
    }
}
