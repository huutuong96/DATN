<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util;

use function trim;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\PhptAssertionFailedError;
use PHPUnit\Framework\SelfDescribing;
use PHPUnit\Runner\ErrorException;
use Throwable;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ThrowableToStringMapper
{
    public static function map(Throwable $t): string
    {
        if ($t instanceof ErrorException) {
            return $t->getMessage();
        }

        if ($t instanceof SelfDescribing) {
            $buffer = $t->toString();

            if ($t instanceof ExpectationFailedException && $t->getComparisonFailure()) {
                $buffer .= $t->getComparisonFailure()->getDiff();
            }

            if ($t instanceof PhptAssertionFailedError) {
                $buffer .= $t->diff();
            }

            if (!empty($buffer)) {
                $buffer = trim($buffer) . "\n";
            }

            return $buffer;
        }

        return $t::class . ': ' . $t->getMessage() . "\n";
    }
}
