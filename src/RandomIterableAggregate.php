<?php

declare(strict_types=1);

namespace loophp\iterators;

use Generator;
use IteratorAggregate;

/**
 * @template TKey
 * @template T
 *
 * @implements IteratorAggregate<TKey, T>
 */
final class RandomIterableAggregate implements IteratorAggregate
{
    /**
     * @var PackIterableAggregate<TKey, T>
     */
    private PackIterableAggregate $iteratorAggregate;

    /**
     * @param iterable<TKey, T> $iterable
     */
    public function __construct(iterable $iterable, private int $seed = 0)
    {
        $this->iteratorAggregate = new PackIterableAggregate($iterable);
    }

    /**
     * @return Generator<TKey, T>
     */
    public function getIterator(): Generator
    {
        mt_srand($this->seed);

        yield from $this->randomize($this->iteratorAggregate, $this->seed);
    }

    /**
     * @param iterable<int, array{0: TKey, 1: T}> $iterable
     *
     * @return Generator<TKey, T>
     */
    private function randomize(iterable $iterable, int $seed): Generator
    {
        $queue = [];

        foreach (new UnpackIterableAggregate($iterable) as $key => $value) {
            if (0 === mt_rand(0, $seed)) {
                yield $key => $value;

                continue;
            }

            $queue[] = [$key, $value];
        }

        if ([] !== $queue) {
            yield from $this->randomize($queue, $seed);
        }
    }
}
