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

use function assert;
use DOMDocument;
use DOMElement;

/**
<<<<<<< HEAD
=======
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class RemoveLogTypes implements Migration
{
    public function migrate(DOMDocument $document): void
    {
        $logging = $document->getElementsByTagName('logging')->item(0);

        if (!$logging instanceof DOMElement) {
            return;
        }

        foreach (SnapshotNodeList::fromNodeList($logging->getElementsByTagName('log')) as $logNode) {
            assert($logNode instanceof DOMElement);

            switch ($logNode->getAttribute('type')) {
                case 'json':
                case 'tap':
                    $logging->removeChild($logNode);
            }
        }
    }
}
