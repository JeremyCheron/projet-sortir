<?php

namespace App\Command;

use App\Services\EventService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'updateEventStatus',
    description: 'Add a short description for your command',
)]
class UpdateEventStatusCommand extends Command
{
    public function __construct(private readonly EventService $eventService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Check events\' status and update if needed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->eventService->updateEvents();



        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
