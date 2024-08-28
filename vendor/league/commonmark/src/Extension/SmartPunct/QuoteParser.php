<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\SmartPunct;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Util\RegexHelper;

final class QuoteParser implements InlineParserInterface
{
<<<<<<< HEAD
    public const DOUBLE_QUOTES = [Quote::DOUBLE_QUOTE, Quote::DOUBLE_QUOTE_OPENER, Quote::DOUBLE_QUOTE_CLOSER];
=======
    /**
     * @deprecated This constant is no longer used and will be removed in a future major release
     */
    public const DOUBLE_QUOTES = [Quote::DOUBLE_QUOTE, Quote::DOUBLE_QUOTE_OPENER, Quote::DOUBLE_QUOTE_CLOSER];

    /**
     * @deprecated This constant is no longer used and will be removed in a future major release
     */
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
    public const SINGLE_QUOTES = [Quote::SINGLE_QUOTE, Quote::SINGLE_QUOTE_OPENER, Quote::SINGLE_QUOTE_CLOSER];

    public function getMatchDefinition(): InlineParserMatch
    {
<<<<<<< HEAD
        return InlineParserMatch::oneOf(...[...self::DOUBLE_QUOTES, ...self::SINGLE_QUOTES]);
=======
        return InlineParserMatch::oneOf(Quote::SINGLE_QUOTE, Quote::DOUBLE_QUOTE);
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
    }

    /**
     * Normalizes any quote characters found and manually adds them to the delimiter stack
     */
    public function parse(InlineParserContext $inlineContext): bool
    {
        $char   = $inlineContext->getFullMatch();
        $cursor = $inlineContext->getCursor();

<<<<<<< HEAD
        $normalizedCharacter = $this->getNormalizedQuoteCharacter($char);

=======
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
        $charBefore = $cursor->peek(-1);
        if ($charBefore === null) {
            $charBefore = "\n";
        }

        $cursor->advance();

        $charAfter = $cursor->getCurrentCharacter();
        if ($charAfter === null) {
            $charAfter = "\n";
        }

        [$leftFlanking, $rightFlanking] = $this->determineFlanking($charBefore, $charAfter);
        $canOpen                        = $leftFlanking && ! $rightFlanking;
        $canClose                       = $rightFlanking;

<<<<<<< HEAD
        $node = new Quote($normalizedCharacter, ['delim' => true]);
        $inlineContext->getContainer()->appendChild($node);

        // Add entry to stack to this opener
        $inlineContext->getDelimiterStack()->push(new Delimiter($normalizedCharacter, 1, $node, $canOpen, $canClose));
=======
        $node = new Quote($char, ['delim' => true]);
        $inlineContext->getContainer()->appendChild($node);

        // Add entry to stack to this opener
        $inlineContext->getDelimiterStack()->push(new Delimiter($char, 1, $node, $canOpen, $canClose));
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec

        return true;
    }

<<<<<<< HEAD
    private function getNormalizedQuoteCharacter(string $character): string
    {
        if (\in_array($character, self::DOUBLE_QUOTES, true)) {
            return Quote::DOUBLE_QUOTE;
        }

        if (\in_array($character, self::SINGLE_QUOTES, true)) {
            return Quote::SINGLE_QUOTE;
        }

        return $character;
    }

=======
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
    /**
     * @return bool[]
     */
    private function determineFlanking(string $charBefore, string $charAfter): array
    {
        $afterIsWhitespace   = \preg_match('/\pZ|\s/u', $charAfter);
        $afterIsPunctuation  = \preg_match(RegexHelper::REGEX_PUNCTUATION, $charAfter);
        $beforeIsWhitespace  = \preg_match('/\pZ|\s/u', $charBefore);
        $beforeIsPunctuation = \preg_match(RegexHelper::REGEX_PUNCTUATION, $charBefore);

        $leftFlanking = ! $afterIsWhitespace &&
            ! ($afterIsPunctuation &&
                ! $beforeIsWhitespace &&
                ! $beforeIsPunctuation);

        $rightFlanking = ! $beforeIsWhitespace &&
            ! ($beforeIsPunctuation &&
                ! $afterIsWhitespace &&
                ! $afterIsPunctuation);

        return [$leftFlanking, $rightFlanking];
    }
}
