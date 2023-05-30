<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user account',
)]
class CreateUserCommand extends Command
{


    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository              $repository)
    {
        parent::__construct();
    }

    protected
    function configure(): void
    {
        $this;
    }

    protected
    function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $io->askQuestion(new Question("User Name:", 'admin'));
        $password = $io->askHidden("Password");
        $email = $io->askQuestion(new Question("User Email:", 'admin@example.com'));
        $rol = $io->askQuestion(new Question("User Rol", 'ROLE_USER')) ?? ['ROLE_USER'];

        $user = new User();

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->setRoles([$rol]);

        try {
            $this->repository->save($user, true);
        } catch (\Throwable $th) {
            $io->error($th->getMessage());
            return Command::FAILURE;
        }

        $io->success(sprintf('User %s account was created!', $username));

        return Command::SUCCESS;
    }
}
