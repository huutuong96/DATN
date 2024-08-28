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

<<<<<<< HEAD
=======
use const DIRECTORY_SEPARATOR;
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
use function basename;
use function dirname;
use function is_dir;
use function mkdir;
use function realpath;
use function str_starts_with;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Filesystem
{
    public static function createDirectory(string $directory): bool
    {
        return !(!is_dir($directory) && !@mkdir($directory, 0o777, true) && !is_dir($directory));
    }

    /**
     * @psalm-param non-empty-string $path
     *
     * @return false|non-empty-string
     */
    public static function resolveStreamOrFile(string $path): false|string
    {
        if (str_starts_with($path, 'php://') || str_starts_with($path, 'socket://')) {
            return $path;
        }

        $directory = dirname($path);

        if (is_dir($directory)) {
            return realpath($directory) . DIRECTORY_SEPARATOR . basename($path);
        }

        return false;
    }
}
