<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Stub;

use function array_shift;
<<<<<<< HEAD
use PHPUnit\Framework\MockObject\Invocation;

/**
=======
use function count;
use PHPUnit\Framework\MockObject\Invocation;
use PHPUnit\Framework\MockObject\NoMoreReturnValuesConfiguredException;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ConsecutiveCalls implements Stub
{
    private array $stack;
<<<<<<< HEAD

    public function __construct(array $stack)
    {
        $this->stack = $stack;
    }

    public function invoke(Invocation $invocation): mixed
    {
=======
    private int $numberOfConfiguredReturnValues;

    public function __construct(array $stack)
    {
        $this->stack                          = $stack;
        $this->numberOfConfiguredReturnValues = count($stack);
    }

    /**
     * @throws NoMoreReturnValuesConfiguredException
     */
    public function invoke(Invocation $invocation): mixed
    {
        if (empty($this->stack)) {
            throw new NoMoreReturnValuesConfiguredException(
                $invocation,
                $this->numberOfConfiguredReturnValues,
            );
        }

>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
        $value = array_shift($this->stack);

        if ($value instanceof Stub) {
            $value = $value->invoke($invocation);
        }

        return $value;
    }
}
