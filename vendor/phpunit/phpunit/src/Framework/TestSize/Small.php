<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\TestSize;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @psalm-immutable
 */
final class Small extends Known
{
    /**
     * @psalm-assert-if-true Small $this
     */
    public function isSmall(): bool
    {
        return true;
    }

    public function isGreaterThan(TestSize $other): bool
    {
        return false;
    }

    public function asString(): string
    {
        return 'small';
    }
}
