<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Clients\Repositories\Clients;
use Symfony\Component\Yaml\Yaml;

/**
 * Clients fixture.
 */
class ClientsFixture extends AbstractFixture
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly Clients $clients
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function purge(): void
    {
        $this->info('Purging clients');
        $clients = $this->clients->getAll();
        foreach ($clients as $client) {
            $this->clients->deleteClient($client['id']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function load(): void
    {
        $this->info('Creating clients');
        $data = Yaml::parseFile(__DIR__ . '/../Fixtures/Clients.yaml');
        foreach ($data as $id => $values) {
            $clientId = $this->clients->addClient($values);
            if ($clientId) {
                $this->setReference($id, $this->clients->getClient($clientId));
            }
        }
    }
}
