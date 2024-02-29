<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

/**
 * Abstract fixture.
 */
abstract class AbstractFixture
{
    use LoggerAwareTrait;
    use LoggerTrait;

    private static array $references = [];

    /**
     * Purge data.
     *
     * @return void
     */
    abstract public function purge(): void;

    /**
     * Load data.
     *
     * @return void
     */
    abstract public function load(): void;

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
    protected function setReference(mixed $id, mixed $value): void
    {
        static::$references[$id] = $value;
    }

    /**
     * Get reference.
     *
     * @return mixed
     */
    protected function getReference(mixed $id): mixed
    {
        return static::$references[$id] ?? null;
    }
}
