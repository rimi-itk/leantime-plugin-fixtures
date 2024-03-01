<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Domain\Projects\Repositories\Projects;
use Symfony\Component\Yaml\Yaml;

/**
 * Projects fixture.
 */
class ProjectsFixture extends AbstractFixture
{
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
    public function purge(): void
    {
        $this->info('Purging projects');
        $projects = $this->projects->getAll(true);
        foreach ($projects as $project) {
            $this->projects->deleteProject($project['id']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function load(): void
    {
        $this->info('Creating projects');
        $data = Yaml::parseFile(__DIR__ . '/../Fixtures/Projects.yaml');
        foreach ($data as $id => $values) {
            $values = $this->expandValues($values);
            $projectId = $this->projects->addProject($values);
            if ($projectId) {
                $this->setReference($id, $this->projects->getProject($projectId));
            }
        }
    }
}
