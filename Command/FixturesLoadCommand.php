<?php

namespace Leantime\Plugins\Fixtures\Command;

use Leantime\Plugins\Fixtures\Services\Fixtures;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Load fixtures command.
 */
#[AsCommand(
    name: 'fixtures:load',
    description: 'Load fixtures',
)]
class FixturesLoadCommand extends Command
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly Fixtures $fixtures
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!$io->confirm('Really load fixtures? This will destroy all data!', !$input->isInteractive())) {
            return Command::SUCCESS;
        }

        $output->setVerbosity($output->getVerbosity() | OutputInterface::VERBOSITY_VERY_VERBOSE);
        $this->fixtures->setLogger(new ConsoleLogger($output));
        $this->fixtures->loadFixtures();

        return Command::SUCCESS;
    }
}
