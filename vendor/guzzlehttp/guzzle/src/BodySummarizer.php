<?php

namespace GuzzleHttp;

use Psr\Http\Message\MessageInterface;

final class BodySummarizer implements BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;

<<<<<<< HEAD
    public function __construct(int $truncateAt = null)
=======
    public function __construct(?int $truncateAt = null)
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
    {
        $this->truncateAt = $truncateAt;
    }

    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string
    {
        return $this->truncateAt === null
<<<<<<< HEAD
            ? \GuzzleHttp\Psr7\Message::bodySummary($message)
            : \GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
=======
            ? Psr7\Message::bodySummary($message)
            : Psr7\Message::bodySummary($message, $this->truncateAt);
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
    }
}
