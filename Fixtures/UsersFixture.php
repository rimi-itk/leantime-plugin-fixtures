<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Users\Repositories\Users;
use Symfony\Component\Yaml\Yaml;

/**
 * Users fixture.
 */
class UsersFixture extends AbstractFixture
{
    protected string $type = 'users';

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
    public function doPurge(): void
    {
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
     * @return array
     */
    public function getFixturesData(): array
    {
        return Yaml::parseFile(__DIR__ . '/../Fixtures/Users.yaml');
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    protected function createFixture(array $values): mixed
    {
        $userId = $this->users->addUser($values);

        return $this->users->getUser($userId);
    }
}
