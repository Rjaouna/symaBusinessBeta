<?php
// src/Command/ResetUserPropertiesCommand.php
namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:reset-user-properties')]
class ResetUserPropertiesCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Reset all user properties to zero at the beginning of each month.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            // Réinitialisez les propriétés ici
            $user->setSim5Usage(0);
            $user->setSim10Usage(0);
            $user->setSim15Usage(0);
            $user->setSim20Usage(0);
            // Ajoutez autant de propriétés que nécessaire

            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $output->writeln('All user properties have been reset to zero.');

        return Command::SUCCESS;
    }
}
