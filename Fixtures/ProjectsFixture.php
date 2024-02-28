<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Projects\Repositories\Projects;
use Leantime\Domain\Users\Repositories\Users;
use Symfony\Component\Yaml\Yaml;

class ProjectsFixture extends AbstractFixture
{
    public function __construct(
        private readonly Projects $projects
    ) {}

    public function purge()
    {
        $this->info('Purging projects');
        $projects = $this->projects->getAll(true);
        foreach ($projects as $project) {
            $this->projects->deleteProject($project['id']);
        }
    }

    public function load()
    {
        $this->info('Creating projects');
        $data = Yaml::parseFile(__DIR__ . '/../Fixtures/Projects.yaml');
        foreach ($data as $id => $values) {
            if (isset($values['assignedUsers'])) {
                foreach ($values['assignedUsers'] as &$user) {
                    if (isset($user['id']) && str_starts_with($user['id'], '@')) {
                        if (($reference = $this->getReference(substr($user['id'], 1)))
                            && isset($reference['id'])
                        ) {
                            $user['id'] = $reference['id'];
                        }
                    }
                }
            }
            $projectId = $this->projects->addProject($values);
            if ($projectId) {
                $this->setReference($id, $this->projects->getProject($projectId));
            }
        }
    }
}
