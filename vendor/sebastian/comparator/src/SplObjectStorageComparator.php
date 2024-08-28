<?php declare(strict_types=1);
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Comparator;

use function assert;
use SebastianBergmann\Exporter\Exporter;
use SplObjectStorage;

final class SplObjectStorageComparator extends Comparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return $expected instanceof SplObjectStorage && $actual instanceof SplObjectStorage;
    }

    /**
     * @throws ComparisonFailure
     */
    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        assert($expected instanceof SplObjectStorage);
        assert($actual instanceof SplObjectStorage);

        $exporter = new Exporter;

        foreach ($actual as $object) {
            if (!$expected->contains($object)) {
                throw new ComparisonFailure(
                    $expected,
                    $actual,
                    $exporter->export($expected),
                    $exporter->export($actual),
<<<<<<< HEAD
                    'Failed asserting that two objects are equal.'
=======
                    'Failed asserting that two objects are equal.',
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
                );
            }
        }

        foreach ($expected as $object) {
            if (!$actual->contains($object)) {
                throw new ComparisonFailure(
                    $expected,
                    $actual,
                    $exporter->export($expected),
                    $exporter->export($actual),
<<<<<<< HEAD
                    'Failed asserting that two objects are equal.'
=======
                    'Failed asserting that two objects are equal.',
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
                );
            }
        }
    }
}
