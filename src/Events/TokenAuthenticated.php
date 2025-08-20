<?php

declare(strict_types=1);

namespace MyDaniel\Paseto\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Event fired when a user is successfully authenticated via a Paseto token.
 */
class TokenAuthenticated
{
    use Dispatchable;

    /**
     * The authenticated user.
     *
     * @var Authenticatable
     */
    public Authenticatable $user;

    /**
     * Create a new event instance.
     *
     * @param  Authenticatable  $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
