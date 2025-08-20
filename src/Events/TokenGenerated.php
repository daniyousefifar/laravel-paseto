<?php

namespace MyDaniel\Paseto\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Event fired when a new Paseto token is generated for a user.
 */
class TokenGenerated
{
    use Dispatchable;

    /**
     * The user for whom the token was generated.
     *
     * @var Authenticatable
     */
    public Authenticatable $user;

    /**
     * The generated Paseto token string.
     *
     * @var string
     */
    public string $token;

    /**
     * Create a new event instance.
     *
     * @param  Authenticatable  $user
     * @param  string  $token
     */
    public function __construct(Authenticatable $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
}
