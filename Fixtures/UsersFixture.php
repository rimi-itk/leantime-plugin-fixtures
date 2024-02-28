<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Users\Repositories\Users;
use Symfony\Component\Yaml\Yaml;

class UsersFixture extends AbstractFixture
{
    public function __construct(
        private readonly Users $users
    ) {}

    public function purge()
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

    public function load()
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
