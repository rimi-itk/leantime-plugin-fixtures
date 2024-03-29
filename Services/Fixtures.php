<?php

namespace Leantime\Plugins\Fixtures\Services;

use Leantime\Plugins\Fixtures\Fixtures\AbstractFixture;
use Leantime\Plugins\Fixtures\Fixtures\ClientsFixture;
use Leantime\Plugins\Fixtures\Fixtures\ProjectsFixture;
use Leantime\Plugins\Fixtures\Fixtures\TicketsFixture;
use Leantime\Plugins\Fixtures\Fixtures\UsersFixture;
use Psr\Log\LoggerAwareTrait;

/**
 * Fixtures data.
 */
class Fixtures
{
    use LoggerAwareTrait;

    /**
     * Constructor
     */
    public function __construct(
        // @TODO Find a more elegant way to inject all fixture classes.
        private readonly ClientsFixture $clientsFixture,
        private readonly UsersFixture $usersFixture,
        private readonly ProjectsFixture $projectsFixture,
        private readonly TicketsFixture $ticketsFixture,
    ) {
    }

    /**
     * Load all fixtures.
     *
     * @return void
     */
    public function loadFixtures(): void
    {
        $fixtures = $this->findFixtures();

        if ($this->logger) {
            foreach ($fixtures as $fixture) {
                $fixture->setLogger($this->logger);
            }
        }

        foreach (array_reverse($fixtures) as $fixture) {
            $fixture->purge();
        }

        foreach ($fixtures as $fixture) {
            $fixture->load();
        }
    }

    /**
     * Find fixtures and return them in the order to load them.
     *
     * @return AbstractFixture[]
     */
    private function findFixtures(): array
    {
        $fixtures = [
            $this->clientsFixture,
            $this->usersFixture,
            $this->projectsFixture,
            $this->ticketsFixture,
        ];

        // @TODO: Sort fixtures topologically on dependencies.

        return $fixtures;
    }
}
