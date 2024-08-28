<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Rule;

use function sprintf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\Invocation as BaseInvocation;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class InvokedAtLeastCount extends InvocationOrder
{
    private readonly int $requiredInvocations;

    public function __construct(int $requiredInvocations)
    {
        $this->requiredInvocations = $requiredInvocations;
    }

    public function toString(): string
    {
        return sprintf(
            'invoked at least %d time%s',
            $this->requiredInvocations,
            $this->requiredInvocations !== 1 ? 's' : '',
        );
    }

    /**
     * Verifies that the current expectation is valid. If everything is OK the
     * code should just return, if not it must throw an exception.
     *
     * @throws ExpectationFailedException
     */
    public function verify(): void
    {
        $actualInvocations = $this->numberOfInvocations();

        if ($actualInvocations < $this->requiredInvocations) {
            throw new ExpectationFailedException(
                sprintf(
                    'Expected invocation at least %d time%s but it occurred %d time%s.',
                    $this->requiredInvocations,
                    $this->requiredInvocations !== 1 ? 's' : '',
                    $actualInvocations,
                    $actualInvocations !== 1 ? 's' : '',
                ),
            );
        }
    }

    public function matches(BaseInvocation $invocation): bool
    {
        return true;
    }
}
