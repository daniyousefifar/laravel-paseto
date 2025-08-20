<?php

namespace MyDaniel\Paseto\Contracts;

use DateTimeImmutable;

/**
 * Interface for the Paseto token builder.
 *
 * Defines the fluent interface for constructing a Paseto token
 * by setting its various claims.
 */
interface PasetoTokenBuilderContract
{
    /**
     * Set the issuer claim (iss).
     *
     * @param  string  $issuer
     *
     * @return self
     */
    public function setIssuer(string $issuer): self;

    /**
     * Set the audience claim (aud).
     *
     * @param  string  $audience
     *
     * @return self
     */
    public function setAudience(string $audience): self;

    /**
     * Set the subject claim (sub).
     *
     * @param  string  $subject
     *
     * @return self
     */
    public function setSubject(string $subject): self;

    /**
     * Set the expiration time claim (exp).
     *
     * @param  DateTimeImmutable|null  $time
     *
     * @return self
     */
    public function setExpiration(?DateTimeImmutable $time = null): self;

    /**
     * Set the JWT ID claim (jti).
     *
     * @param  string  $jwtId
     *
     * @return self
     */
    public function setJti(string $jwtId): self;

    /**
     * Set custom claims for the token.
     *
     * @param  array  $claims
     *
     * @return self
     */
    public function setClaims(array $claims): self;

    /**
     * Set the not-before time claim (nbf).
     *
     * @param  DateTimeImmutable|null  $time
     *
     * @return self
     */
    public function setNotBefore(?DateTimeImmutable $time = null): self;

    /**
     * Build and return the final Paseto token string.
     *
     * @return string
     */
    public function getToken(): string;
}
