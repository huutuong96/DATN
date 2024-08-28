<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject\Generator;

use function class_exists;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @deprecated https://github.com/sebastianbergmann/phpunit/issues/5243
 */
final class MockTrait implements MockType
{
    private readonly string $classCode;

    /**
     * @psalm-var class-string
     */
    private readonly string $mockName;

    /**
     * @psalm-param class-string $mockName
     */
    public function __construct(string $classCode, string $mockName)
    {
        $this->classCode = $classCode;
        $this->mockName  = $mockName;
    }

    /**
     * @psalm-return class-string
     */
    public function generate(): string
    {
        if (!class_exists($this->mockName, false)) {
            eval($this->classCode);
        }

        return $this->mockName;
    }
}
