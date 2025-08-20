<?php

namespace MyDaniel\Paseto\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;

/**
 * Interface for a model that can be the subject of a Paseto token.
 *
 * This contract ensures that the Authenticatable model (typically the User model)
 * provides all the necessary claims for generating a Paseto token.
 */
interface PasetoSubject extends Authenticatable
{
    /**
     * Get the unique identifier for the token (jti claim).
     *
     * @return string
     */
    public function getJwtId(): string;

    /**
     * Get the subject identifier for the token (sub claim).
     *
     * @return string
     */
    public function getJwtSubjectId(): string;

    /**
     * Get the 'not before' time for the token (nbf claim).
     *
     * @return Carbon|null
     */
    public function getJwtValidFromTime(): ?Carbon;

    /**
     * Get the expiration time for the token (exp claim).
     *
     * @return Carbon|null
     */
    public function getJwtValidUntilTime(): ?Carbon;

    /**
     * Get any custom claims to be added to the token payload.
     *
     * @return array
     */
    public function getJwtCustomClaims(): array;
}
