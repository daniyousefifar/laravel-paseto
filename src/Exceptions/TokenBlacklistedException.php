<?php

namespace MyDaniel\Paseto\Exceptions;

/**
 * Thrown when a token is found in the blacklist.
 *
 * This typically occurs when a user has logged out, and their token
 * has been invalidated before its natural expiration time.
 */
class TokenBlacklistedException extends PasetoException
{
    /**
     * Create a new TokenBlacklistedException instance.
     *
     * @param  string  $message  The exception message.
     * @param  int  $code  The exception code.
     * @param  \Throwable|null  $previous  The previous throwable used for the exception chaining.
     */
    public function __construct(
        string $message = 'Token has been blacklisted',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
