<?php

namespace MyDaniel\Paseto\Exceptions;

/**
 * Thrown when a token is invalid for any reason other than expiration or being blacklisted.
 *
 * This can include issues such as a malformed token, an invalid signature,
 * or failure to meet other validation rules (e.g., issuer, audience).
 */
class TokenInvalidException extends PasetoException
{
    /**
     * Create a new TokenInvalidException instance.
     *
     * @param  string  $message  The exception message.
     * @param  int  $code  The exception code.
     * @param  \Throwable|null  $previous  The previous throwable used for the exception chaining.
     */
    public function __construct(string $message = 'Token is invalid', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
