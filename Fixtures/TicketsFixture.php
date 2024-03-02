<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Tickets\Repositories\Tickets;
use Symfony\Component\Yaml\Yaml;

/**
 * Tickets fixture.
 */
class TicketsFixture extends AbstractFixture
{
    protected string $type = 'tickets';

    /**
     * Constructor.
     */
    public function __construct(
        private readonly Tickets $tickets
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function doPurge(): void
    {
        $tickets = $this->tickets->getAllBySearchCriteria([]);
        foreach ($tickets as $ticket) {
            $this->tickets->deleteTicket($ticket['id']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getFixturesData(): array
    {
        return Yaml::parseFile(__DIR__ . '/../Fixtures/Tickets.yaml');
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    protected function createFixture(array $values): mixed
    {
        $ticketId = $this->tickets->addTicket($values);

        return $this->tickets->getTicket($ticketId);
    }
}
