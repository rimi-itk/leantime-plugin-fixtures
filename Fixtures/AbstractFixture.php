<?php

namespace Leantime\Plugins\Fixtures\Fixtures;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;

abstract class AbstractFixture
{
    use LoggerAwareTrait;
    use LoggerTrait;

    private static array $references = [];

    abstract public function purge();

    abstract public function load();

    public function log($level, $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    protected function setReference(mixed $id, mixed $value): void
    {
        static::$references[$id] = $value;
    }

    protected function getReference(mixed $id): mixed
    {
        return static::$references[$id] ?? null;
    }
}
