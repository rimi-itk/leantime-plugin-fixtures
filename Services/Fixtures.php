<?php

namespace Leantime\Plugins\Fixtures\Services;

use Leantime\Plugins\Fixtures\Fixtures\AbstractFixture;
use Leantime\Plugins\Fixtures\Fixtures\ProjectsFixture;
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
        private readonly UsersFixture $usersFixture,
        private readonly ProjectsFixture $projectsFixture
    ) {}

    public function loadFixtures()
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
        return [
            $this->usersFixture,
            $this->projectsFixture,
        ];
    }
}
