<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Clients\Repositories\Clients;
use Symfony\Component\Yaml\Yaml;

/**
 * Clients fixture.
 */
class ClientsFixture extends AbstractFixture
{
    protected string $type = 'clients';

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
    protected function doPurge(): void
    {
        $clients = $this->clients->getAll();
        foreach ($clients as $client) {
            $this->clients->deleteClient($client['id']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getFixturesData(): array
    {
        return Yaml::parseFile(__DIR__ . '/../Fixtures/Clients.yaml');
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    protected function createFixture(array $values): mixed
    {
        $clientId = $this->clients->addClient($values);

        return $this->clients->getClient($clientId);
    }
}
