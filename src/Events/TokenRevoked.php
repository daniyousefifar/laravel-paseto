<?php

declare(strict_types=1);

namespace MyDaniel\Paseto\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * Event fired when a Paseto token is revoked (blacklisted).
 */
class TokenRevoked
{
    use Dispatchable;

    /**
     * The payload of the token that was revoked.
     *
     * @var array
     */
    public array $payload;

    /**
     * Create a new event instance.
     *
     * @param  array  $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}
