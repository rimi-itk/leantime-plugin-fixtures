<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Users\Repositories\Users;
use Symfony\Component\Yaml\Yaml;

/**
 * Users fixture.
 */
class UsersFixture extends AbstractFixture
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly Users $users
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function purge(): void
    {
        $this->info('Purging users');
        $users = array_merge(
            // This does not include API users.
            $this->users->getAll(),
            $this->users->getAllBySource('api')
        );
        foreach ($users as $user) {
            $this->users->deleteUser($user['id']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function load(): void
    {
        $this->info('Creating users');
        $data = Yaml::parseFile(__DIR__ . '/../Fixtures/Users.yaml');
        foreach ($data as $id => $values) {
            $userId = $this->users->addUser($values);
            if ($userId) {
                $this->setReference($id, $this->users->getUser($userId));
            }
        }
    }
}
