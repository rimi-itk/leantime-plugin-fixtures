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
    protected function setReference(string $id, mixed $value): void
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
    protected function getReference(mixed $id): mixed
    {
        return static::$references[$id] ?? null;
    }

    /**
     * Expand values.
     *
     * @return array
     */
    protected function expandValues(array $values): array
    {
        array_walk($values, $this->expandValue(...));

        return $values;
    }

    /**
     * Expand value.
     *
     * Inspired by https://github.com/theofidry/AliceBundle
     *
     * @return void
     */
    protected function expandValue(mixed &$value): void
    {
        if (is_array($value)) {
            foreach ($value as $key => &$val) {
                $this->expandValue($val);
            }
        }
        if (is_string($value) && str_starts_with($value, '<')) {
            if (preg_match('/^<ref(?:erence)?\((?P<id>[^)]+)\)>$/', $value, $matches)) {
                // <reference(id)> => Get ID from named reference
                // <ref(id)> is the same as <reference(id)>
                $id = $matches['id'];
                $key = $matches['key'] ?? 'id';
                $reference = $this->getReference($id);
                if (empty($reference)) {
                    throw new \RuntimeException(sprintf('Invalid reference: %s', $id));
                }
                if (!isset($reference[$key])) {
                    throw new \RuntimeException(sprintf('Invalid key in reference %s: %s', $id, $key));
                }
                $value = $reference[$key];
            } elseif (preg_match('/^<date(?P<time>time)?\((?P<spec>[^)]*)\)>$/', $value, $matches)) {
                // <datetime(spec)> => yyyy-mm-dd hh:mm:ss
                // <date(spec)> => yyyy-mm-dd
                $datetime = new \DateTimeImmutable($matches['spec'] ?? 'now');
                $value = $datetime->format($matches['time'] ? 'Y-m-d H:i:s' : 'Y-m-d');
            } else {
                throw new \RuntimeException(sprintf('Invalid expression: %s', $value));
            }
        }
    }
}
