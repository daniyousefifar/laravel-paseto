<?php

declare(strict_types=1);

namespace MyDaniel\Paseto\Contracts;

/**
 * Interface for a Paseto token blacklist.
 *
 * Defines the contract for adding and checking tokens in a blacklist.
 * Implementations of this interface are responsible for managing the storage
 * of invalidated tokens.
 */
interface BlacklistContract
{
    /**
     * Add a token's JTI to the blacklist for its remaining lifetime.
     *
     * @param  array  $payload  The token payload containing 'jti' and 'exp'.
     *
     * @return bool True if the token was successfully blacklisted, false otherwise.
     */
    public function add(array $payload): bool;

    /**
     * Check if a token's JTI is in the blacklist.
     *
     * @param  array  $payload  The token payload containing the 'jti'.
     *
     * @return bool True if the token is blacklisted, false otherwise.
     */
    public function has(array $payload): bool;
}
