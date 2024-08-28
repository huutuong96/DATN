<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Telemetry;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class System
{
    private readonly StopWatch $stopWatch;
    private readonly MemoryMeter $memoryMeter;
    private readonly GarbageCollectorStatusProvider $garbageCollectorStatusProvider;

    public function __construct(StopWatch $stopWatch, MemoryMeter $memoryMeter, GarbageCollectorStatusProvider $garbageCollectorStatusProvider)
    {
        $this->stopWatch                      = $stopWatch;
        $this->memoryMeter                    = $memoryMeter;
        $this->garbageCollectorStatusProvider = $garbageCollectorStatusProvider;
    }

    public function snapshot(): Snapshot
    {
        return new Snapshot(
            $this->stopWatch->current(),
            $this->memoryMeter->memoryUsage(),
            $this->memoryMeter->peakMemoryUsage(),
            $this->garbageCollectorStatusProvider->status(),
        );
    }
}
