<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

use function sprintf;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ComparisonMethodDoesNotAcceptParameterTypeException extends Exception
{
    public function __construct(string $className, string $methodName, string $type)
    {
        parent::__construct(
            sprintf(
                '%s is not an accepted argument type for comparison method %s::%s().',
                $type,
                $className,
                $methodName,
            ),
        );
    }
}
