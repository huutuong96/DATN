<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

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
final class SuccessfulSchemaDetectionResult extends SchemaDetectionResult
{
    /**
     * @psalm-var non-empty-string
     */
    private readonly string $version;

    /**
     * @psalm-param non-empty-string $version
     */
    public function __construct(string $version)
    {
        $this->version = $version;
    }

    /**
     * @psalm-assert-if-true SuccessfulSchemaDetectionResult $this
     */
    public function detected(): bool
    {
        return true;
    }

    /**
     * @psalm-return non-empty-string
     */
    public function version(): string
    {
        return $this->version;
    }
}
