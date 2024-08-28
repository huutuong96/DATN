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

use function count;
use ArrayIterator;
use Countable;
use DOMNode;
use DOMNodeList;
use IteratorAggregate;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @template-implements IteratorAggregate<int, DOMNode>
 */
final class SnapshotNodeList implements Countable, IteratorAggregate
{
    /**
     * @psalm-var list<DOMNode>
     */
    private array $nodes = [];

    public static function fromNodeList(DOMNodeList $list): self
    {
        $snapshot = new self;

        foreach ($list as $node) {
            $snapshot->nodes[] = $node;
        }

        return $snapshot;
    }

    public function count(): int
    {
        return count($this->nodes);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->nodes);
    }
}
