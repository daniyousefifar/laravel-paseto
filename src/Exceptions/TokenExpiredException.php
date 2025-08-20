<?php

namespace MyDaniel\Paseto\Exceptions;

/**
 * Thrown when a token's expiration time (exp claim) has passed.
 *
 * This indicates that the token is no longer valid due to its age and
 * the request should be re-authenticated.
 */
class TokenExpiredException extends PasetoException
{
    /**
     * Create a new TokenExpiredException instance.
     *
     * @param  string  $message  The exception message.
     * @param  int  $code  The exception code.
     * @param  \Throwable|null  $previous  The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = 'Token has expired', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
