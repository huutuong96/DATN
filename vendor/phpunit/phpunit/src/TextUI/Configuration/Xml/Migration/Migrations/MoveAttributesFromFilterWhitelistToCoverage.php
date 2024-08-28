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
final class MoveAttributesFromFilterWhitelistToCoverage implements Migration
{
    /**
     * @throws MigrationException
     */
    public function migrate(DOMDocument $document): void
    {
        $whitelist = $document->getElementsByTagName('whitelist')->item(0);

        if (!$whitelist) {
            return;
        }

        $coverage = $document->getElementsByTagName('coverage')->item(0);

        if (!$coverage instanceof DOMElement) {
            throw new MigrationException('Unexpected state - No coverage element');
        }

        $map = [
            'addUncoveredFilesFromWhitelist'     => 'includeUncoveredFiles',
            'processUncoveredFilesFromWhitelist' => 'processUncoveredFiles',
        ];

        foreach ($map as $old => $new) {
            if (!$whitelist->hasAttribute($old)) {
                continue;
            }

            $coverage->setAttribute($new, $whitelist->getAttribute($old));
            $whitelist->removeAttribute($old);
        }
    }
}
