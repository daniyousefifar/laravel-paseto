<?php

namespace MyDaniel\Paseto;

use Illuminate\Cache\Repository as Cache;
use MyDaniel\Paseto\Contracts\BlacklistContract;

/**
 * Manages the blacklisting of Paseto tokens.
 *
 * This class uses the Laravel cache to store the JTI (JWT ID) of tokens
 * that have been invalidated (e.g., after a user logs out). The tokens
 * are stored until their original expiration time.
 */
final class Blacklist implements BlacklistContract
{
    /**
     * The cache repository instance.
     *
     * @var Cache
     */
    private Cache $cache;

    /**
     * Create a new Blacklist instance.
     *
     * @param  Cache  $cache  The cache repository.
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Add a token's JTI to the blacklist for its remaining lifetime.
     *
     * Only adds the token to the blacklist if it has not already expired.
     *
     * @param  array  $payload  The token payload containing 'jti' and 'exp'.
     *
     * @return bool True if the token was successfully blacklisted, false otherwise.
     */
    public function add(array $payload): bool
    {
        $expiration = $this->getRemainingSeconds($payload['exp']);

        // Only add to blacklist if the token has not already expired.
        if ($expiration > 0) {
            return $this->cache->put($this->getKey($payload), 'blacklisted', $expiration);
        }

        return false;
    }

    /**
     * Check if a token's JTI is in the blacklist.
     *
     * @param  array  $payload  The token payload containing the 'jti'.
     *
     * @return bool True if the token is blacklisted, false otherwise.
     */
    public function has(array $payload): bool
    {
        return $this->cache->has($this->getKey($payload));
    }

    /**
     * Get the cache key for the given payload.
     *  The key is prefixed to avoid collisions in the cache.
     *
     * @param  array  $payload  The token payload containing the 'jti'.
     *
     * @return string The unique cache key for the token.
     */
    private function getKey(array $payload): string
    {
        return 'paseto_blacklist:'.$payload['jti'];
    }

    /**
     * Get the remaining time until the token expires (in seconds).
     *
     * @param  string  $expirationTimestamp  The ISO 8601 timestamp of expiration.
     *
     * @return int The remaining seconds. Returns 0 if the token has expired.
     */
    private function getRemainingSeconds(string $expirationTimestamp): int
    {
        $exp = strtotime($expirationTimestamp);
        $now = time();

        if ($exp === false) {
            return 0;
        }

        return max(0, $exp - $now);
    }
}
