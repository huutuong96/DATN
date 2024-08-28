<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework\MockObject;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This trait is not covered by the backward compatibility promise for PHPUnit
 */
trait ProxiedCloneMethod
{
    public function __clone(): void
    {
        $this->__phpunit_invocationMocker = clone $this->__phpunit_getInvocationHandler();

        parent::__clone();
    }
}
