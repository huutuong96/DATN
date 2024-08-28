<?php

namespace GuzzleHttp;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface MessageFormatterInterface
{
    /**
     * Returns a formatted message string.
     *
     * @param RequestInterface       $request  Request that was sent
     * @param ResponseInterface|null $response Response that was received
     * @param \Throwable|null        $error    Exception that was received
     */
<<<<<<< HEAD
    public function format(RequestInterface $request, ResponseInterface $response = null, \Throwable $error = null): string;
=======
    public function format(RequestInterface $request, ?ResponseInterface $response = null, ?\Throwable $error = null): string;
>>>>>>> 64449045de4953f33495614cf40cae6b40a0b6ec
}
