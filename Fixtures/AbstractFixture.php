<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Leantime\Plugins\Fixtures\Services\FixtureDataProcessor;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;
use Symfony\Component\Yaml\Yaml;

/**
 * Abstract fixture.
 */
abstract class AbstractFixture
{
    use LoggerAwareTrait;
    use LoggerTrait;

    protected string $type;

    private static array $references = [];

    /**
     * Purge data.
     *
     * @return void
     */
    public function purge(): void
    {
        $this->info(sprintf('Purging %s', $this->type));

        $this->doPurge();
    }

    /**
     * Do purge.
     *
     * @return void
     */
    abstract protected function doPurge(): void;

    /**
     * Load data.
     *
     * @return void
     */
    public function load(): void
    {
        $this->info(sprintf('Loading %s', $this->type));

        $data = $this->getFixturesData();
        $data = (new FixtureDataProcessor())->expandValues($data);
        foreach ($data as $id => $values) {
            $fixture = $this->createFixture($values);
            $this->debug(json_encode($fixture, JSON_PRETTY_PRINT));
            if ($fixture) {
                $this->setReference($id, $fixture);
            }
        }
    }

    /**
     * Get fixtures data.
     *
     * @return array
     */
    protected function getFixturesData(): array
    {
        $name = preg_replace('@.+\\\\(.+)Fixture$@', '\1', static::class);
        $path = __DIR__ . '/../Fixtures/' . $name . '.yaml';
        if (!is_readable($path)) {
            throw new \RuntimeException(sprintf('Cannot read fixtures path %s', $path));
        }

        return Yaml::parseFile($path);
    }

    /**
     * Create fixture.
     *
     * @return mixed
     */
    abstract protected function createFixture(array $values): mixed;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Set reference.
     *
     * @return void
     */
    public static function setReference(string $id, mixed $value): void
    {
        if (isset(static::$references[$id])) {
            throw new \RuntimeException(sprintf('Duplicate reference: %s', $id));
        }
        static::$references[$id] = $value;
    }

    /**
     * Get reference.
     *
     * @return mixed
     */
    public static function getReference(mixed $id): mixed
    {
        return static::$references[$id] ?? null;
    }
}
