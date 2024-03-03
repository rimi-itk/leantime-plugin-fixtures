<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Projects\Repositories\Projects;
use Symfony\Component\Yaml\Yaml;

/**
 * Projects fixture.
 */
class ProjectsFixture extends AbstractFixture
{
    protected string $type = 'projects';

    /**
     * Constructor.
     */
    public function __construct(
        private readonly Projects $projects
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function doPurge(): void
    {
        $projects = $this->projects->getAll(true);
        foreach ($projects as $project) {
            $this->projects->deleteProject($project['id']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    protected function createFixture(array $values): mixed
    {
        $projectId = $this->projects->addProject($values);

        return $this->projects->getProject($projectId);
    }
}
