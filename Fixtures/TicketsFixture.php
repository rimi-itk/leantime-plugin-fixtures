<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Tickets\Repositories\Tickets;
use Symfony\Component\Yaml\Yaml;

/**
 * Tickets fixture.
 */
class TicketsFixture extends AbstractFixture
{
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
    public function purge(): void
    {
        $this->info('Purging tickets');
        $tickets = $this->tickets->getAllBySearchCriteria([]);
        foreach ($tickets as $ticket) {
            $this->tickets->deleteTicket($ticket['id']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function load(): void
    {
        $this->info('Creating tickets');
        $data = Yaml::parseFile(__DIR__ . '/../Fixtures/Tickets.yaml');
        foreach ($data as $id => $values) {
            foreach (['projectId', 'userId'] as $referenceKey) {
                if (isset($values[$referenceKey])) {
                    $id = $values[$referenceKey];
                    if (str_starts_with($id, '@')) {
                        if (
                            ($reference = $this->getReference(substr($id, 1)))
                            && isset($reference['id'])
                        ) {
                            $values[$referenceKey] = $reference['id'];
                        } else {
                            header('content-type: text/plain');
                            echo var_export([$id, $reference, array_keys(static::$references)], true);
                            die(__FILE__ . ':' . __LINE__ . ':' . __METHOD__);
                        }
                    }
                }
            }

            $ticketId = $this->tickets->addTicket($values);
            if ($ticketId) {
                $this->setReference($id, $this->tickets->getTicket($ticketId));
            }
        }
    }
}
