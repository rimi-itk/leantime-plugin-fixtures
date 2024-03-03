<?php

declare(strict_types=1);

namespace Leantime\Plugins\Fixtures\Services;

use Leantime\Plugins\Fixtures\Fixtures\AbstractFixture;

/**
 * Fixture data processor.
 */
final class FixtureDataProcessor
{
    /**
     * Used only for testing.
     *
     * @var null|\DateTimeInterface
     */
    private ?\DateTimeInterface $now = null;

    /**
     * Expand values.
     *
     * @return array
     */
    public function expandValues(array $values): array
    {
        $ranges = [];
        foreach ($values as $key => $value) {
            if (preg_match('/^(?P<prefix>.+_)\{(?P<start>[^.]+)\.{2}(?P<end>[^)]+)\}$/', $key, $matches)) {
                foreach (range($matches['start'], $matches['end']) as $current) {
                    $clone = $value;
                    array_walk($clone, fn (mixed &$value) => $this->expandCurrent($value, $current));
                    $ranges[$matches['prefix'] . $current] = $clone;
                }
            } else {
                $ranges[$key] = $value;
            }
        }

        $values = $ranges;

        array_walk($values, $this->expandValue(...));

        return $values;
    }

    /**
     * Expand current() expression.
     *
     * @return void
     */
    private function expandCurrent(mixed &$value, int|string $current)
    {
        if (is_array($value)) {
            foreach ($value as $key => &$val) {
                $this->expandCurrent($val, $current);
            }
        } elseif (is_string($value)) {
            // Replace current() inside expressions
            $value = preg_replace('/(<[^>]+)current\(\)([^>]+>)/', '${1}' . $current . '${2}', $value);
            // Evaluate current as an expression.
            $value = str_replace('<current()>', (string)$current, $value);
            if (is_numeric($value)) {
                $value = (int)$value;
            }
        }
    }

    /**
     * Expand value.
     *
     * Inspired by https://github.com/nelmio/alice
     *
     * @return void
     */
    public function expandValue(mixed &$value): void
    {
        if (is_array($value)) {
            foreach ($value as $key => &$val) {
                $this->expandValue($val);
            }
        } elseif (is_string($value) && preg_match('/[<@]/', $value)) {
            $value = preg_replace_callback(
                '/<(?P<expression>[^>]+)>/',
                $this->evaluateExpression(...),
                $value
            );

            // Expand references
            //   @reference->property
            //   @reference is a shorthand for @reference->id
            $value = preg_replace_callback(
                '/(?<!\\\\)@(?P<id>[a-z0-9_]+)(?:->(?P<property>[a-z0-9_]+))?/i',
                $this->expandReference(...),
                $value
            );

            // Unescape escaped references.
            $value = str_replace('\\@', '@', $value);
        }
    }

    /**
     * Used only for testing.
     *
     * @return void
     */
    public function setNow(\DateTimeInterface $now): void
    {
        $this->now = $now;
    }

    /**
     * Used only for testing.
     *
     * @return \DateTimeInterface
     */
    private function getNow(): \DateTimeInterface
    {
        return $this->now ?? new \DateTimeImmutable();
    }

    /**
     * Expand reference.
     *
     * @return mixed
     */
    private function expandReference(array $matches): mixed
    {
        [$id, $property] = [$matches['id'], $matches['property'] ?? 'id'];

        $reference = AbstractFixture::getReference($id);
        if (empty($reference)) {
            throw new \RuntimeException(sprintf('Invalid reference: %s', $id));
        }
        if (null === $property) {
            return $reference;
        }

        return match (true) {
            is_array($reference) && isset($property, $reference) => $reference[$property],
            isset($reference->{$property}) => $reference->{$property},
            default => throw new \RuntimeException(sprintf('Invalid reference property: %s', $property))
        };
    }

    /**
     * Evaluate expression.
     *
     * @return mixed
     */
    private function evaluateExpression(array $matches): mixed
    {
        $expression = $matches['expression'];

        if (preg_match('/^mod\((?P<a>[0-9]+)\s*,\s*(?P<b>[0-9]+)\)$/', $expression, $matches)) {
            return (int)$matches['a'] % (int)$matches['b'];
        }

        if (preg_match('/^(?P<name>date(?P<time>time)?|time)\((?P<spec>[^)]*)\)$/', $expression, $matches)) {
            // <date(spec)> => yyyy-mm-dd
            // <datetime(spec)> => yyyy-mm-dd hh:mm:ss
            // <time(spec)> => hh:mm:ss
            $datetime = $matches['spec'] ? new \DateTimeImmutable($matches['spec']) : $this->getNow();
            $format = match ($matches['name']) {
                'datetime' => 'Y-m-d H:i:s',
                'date' => 'Y-m-d',
                'time' => 'H:i:s',
            };

            return $datetime->format($format);
        }

        throw new \RuntimeException(sprintf('Invalid expression: %s', $expression));
    }
}
