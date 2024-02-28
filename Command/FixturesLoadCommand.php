<?php

namespace Leantime\Plugins\Fixtures\Command;

use Leantime\Plugins\Fixtures\Services\Fixtures;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'fixtures:load',
    description: 'Load fixtures',
)]
class FixturesLoadCommand extends Command
{
    public function __construct(
        private readonly Fixtures $fixtures
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->fixtures->setLogger(new ConsoleLogger($output));
        $this->fixtures->loadFixtures();

        return Command::SUCCESS;
    }
}
