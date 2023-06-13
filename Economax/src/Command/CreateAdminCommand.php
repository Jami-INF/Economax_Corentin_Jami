<?php

namespace App\Command;

use App\Service\UserRegister;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolationList;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Add a short description for your command',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        protected UserRegister $userRegister,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to create an admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text([
            '',
            '===================',
            'Admin Creator',
            '===================',
            '',
        ]);

        $email = $io->ask('Email address of super admin');
        $username = $io->ask('Username');
        $password = $io->askHidden('Password of admin');

        $register = $this->userRegister->addAdmin($email, $password, $username);

        if ($register instanceof ConstraintViolationList) {
            foreach ($register as $error) {
                $io->error($error->getMessage());
            }

            return Command::FAILURE;
        }

        $io->success('Admin Created');

        return Command::SUCCESS;
    }
}
