<?php

namespace MyDaniel\Paseto\Contracts;

use ParagonIE\Paseto\JsonToken;

/**
 * Interface for the Paseto token parser.
 *
 * Defines the contract for parsing and validating a Paseto token string.
 */
interface PasetoTokenParserContract
{
    /**
     * Add a validation rule to check the token's issuer (iss).
     *
     * @param  string  $issuer  The expected issuer.
     *
     * @return self
     */
    public function setIssuedBy(string $issuer): self;

    /**
     * Add a validation rule to check the token's audience (aud).
     *
     * @param  string  $audience  The expected audience.
     *
     * @return self
     */
    public function setForAudience(string $audience): self;

    /**
     * Parse and validate the given token string.
     *
     * @param  string  $token  The encoded Paseto token.
     *
     * @return JsonToken The parsed and validated token object.
     */
    public function parse(string $token): JsonToken;
}
