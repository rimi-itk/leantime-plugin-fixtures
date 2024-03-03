<?php

declare(strict_types=1);

namespace Leantime\Plugins\Fixtures\Services;

use Leantime\Plugins\Fixtures\Fixtures\AbstractFixture;
use PHPUnit\Framework\TestCase;

/**
 * Tests form FixtureDataProcessor.
 */
final class FixtureDataProcessorTest extends TestCase
{
    private static \DateTimeInterface $now;

    /**
     * @dataProvider provider
     *
     * @return void
     */
    public function testProcessValues(array $values, array $expected): void
    {
        $processor = new FixtureDataProcessor();
        $processor->setNow(static::$now);

        $actual = $processor->expandValues($values);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Provider.
     *
     * @return iterable
     */
    public static function provider(): iterable
    {
        static::$now = new \DateTimeImmutable('2001-01-01');

        AbstractFixture::setReference('item', ['id' => 87]);

        yield [
            [
                'item' => '@item',
                'email' => 'test\@example.com',
            ],
            [
                'item' => 87,
                'email' => 'test@example.com',
            ],
        ];

        yield [
            [
                'item' => '@item->id',
            ],
            [
                'item' => 87,
            ],
        ];

        yield [
            [
                'datetime' => 'The current time is <time()>.',
            ],
            [
                'datetime' => sprintf('The current time is %s.', static::$now->format('H:i:s')),
            ],
        ];

        yield [
            [],
            [],
        ];

        yield [
            [
                'date' => '<date(2001-01-01)>',
            ],
            [
                'date' => '2001-01-01',
            ],
        ];

        yield [
            [
                'datetime' => '<datetime()>',
            ],
            [
                'datetime' => '2001-01-01 00:00:00',
            ],
        ];

        yield [
            [
                'item' => [
                    'createdAt' => '<datetime()>',
                ],
            ],
            [
                'item' => [
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                ],
            ],
        ];

        yield [
            [
                'item_{0..1}' => [
                    'id' => '<current()>',
                    'ids' => ['<current()>'],
                ],
            ],
            [
                'item_0' => [
                    'id' => 0,
                    'ids' => [0],
                ],
                'item_1' => [
                    'id' => 1,
                    'ids' => [1],
                ],
            ],
        ];

            yield [
            [
                'item_{0..9}' => [
                    'id' => '<current()>',
                    'name' => 'Item <current()>',
                    'createdAt' => '<datetime()>',
                    'client' => '<mod(current(), 4)>',
                ],
            ],
            [
                'item_0' => [
                    'id' => 0,
                    'name' => 'Item 0',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '0',
                ],
                'item_1' => [
                    'id' => 1,
                    'name' => 'Item 1',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '1',
                ],
                'item_2' => [
                    'id' => 2,
                    'name' => 'Item 2',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '2',
                ],
                'item_3' => [
                    'id' => 3,
                    'name' => 'Item 3',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '3',
                ],
                'item_4' => [
                    'id' => 4,
                    'name' => 'Item 4',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '0',
                ],
                'item_5' => [
                    'id' => 5,
                    'name' => 'Item 5',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '1',
                ],
                'item_6' => [
                    'id' => 6,
                    'name' => 'Item 6',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '2',
                ],
                'item_7' => [
                    'id' => 7,
                    'name' => 'Item 7',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '3',
                ],
                'item_8' => [
                    'id' => 8,
                    'name' => 'Item 8',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '0',
                ],
                'item_9' => [
                    'id' => 9,
                    'name' => 'Item 9',
                    'createdAt' => static::$now->format('Y-m-d H:i:s'),
                    'client' => '1',
                ],
            ],
            ];
    }
}
