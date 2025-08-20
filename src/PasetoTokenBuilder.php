<?php

declare(strict_types=1);

namespace MyDaniel\Paseto;

use DateTimeImmutable;
use MyDaniel\Paseto\Contracts\PasetoTokenBuilderContract;
use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\Exception\InvalidKeyException;
use ParagonIE\Paseto\Exception\InvalidPurposeException;
use ParagonIE\Paseto\Exception\PasetoException;
use ParagonIE\Paseto\Keys\Base\SymmetricKey;
use ParagonIE\Paseto\Protocol\Version4;
use ParagonIE\Paseto\Purpose;

/**
 * A fluent builder for creating Paseto tokens.
 *
 * This class provides a chainable interface to set various claims
 * and generate a v4 local (encrypted) Paseto token.
 */
final class PasetoTokenBuilder implements PasetoTokenBuilderContract
{
    /**
     * The underlying Paseto builder instance from the paragonie/paseto library.
     *
     * @var Builder
     */
    private Builder $builder;

    /**
     * Create a new PasetoTokenBuilder instance.
     *
     * @param  SymmetricKey  $sharedKey  The symmetric key for encryption.
     *
     * @throws InvalidPurposeException
     * @throws InvalidKeyException
     * @throws PasetoException
     */
    public function __construct(SymmetricKey $sharedKey)
    {
        $this->builder = (new Builder())
            ->setKey($sharedKey)
            ->setVersion(new Version4())
            ->setPurpose(Purpose::local())
            ->setIssuedAt();
    }

    /**
     * Set the issuer claim (iss).
     *
     * @param  string  $issuer  The issuer URL or identifier.
     *
     * @return self
     */
    public function setIssuer(string $issuer): self
    {
        $this->builder->setIssuer($issuer);

        return $this;
    }

    /**
     * Set the audience claim (aud).
     *
     * @param  string  $audience  The audience URL or identifier.
     *
     * @return self
     */
    public function setAudience(string $audience): self
    {
        $this->builder->setAudience($audience);

        return $this;
    }

    /**
     * Set the subject claim (sub).
     *
     * @param  string  $subject  The subject identifier (e.g., user ID).
     *
     * @return self
     */
    public function setSubject(string $subject): self
    {
        $this->builder->setSubject($subject);

        return $this;
    }

    /**
     * Set the expiration time claim (exp).
     *
     * @param  DateTimeImmutable|null  $time  The expiration time.
     *
     * @return self
     */
    public function setExpiration(?DateTimeImmutable $time = null): self
    {
        $this->builder->setExpiration($time);

        return $this;
    }

    /**
     * Set the JWT ID claim (jti).
     *
     * @param  string  $jwtId  A unique identifier for the token.
     *
     * @return self
     */
    public function setJti(string $jwtId): self
    {
        $this->builder->setJti($jwtId);

        return $this;
    }

    /**
     * Set custom claims for the token.
     *
     * @param  array  $claims  An associative array of custom claims.
     *
     * @return self
     */
    public function setClaims(array $claims): self
    {
        $this->builder->setClaims($claims);

        return $this;
    }

    /**
     * Set the not-before time claim (nbf).
     *
     * @param  DateTimeImmutable|null  $time  The time before which the token must not be accepted.
     *
     * @return self
     */
    public function setNotBefore(?DateTimeImmutable $time = null): self
    {
        $this->builder->setNotBefore($time);

        return $this;
    }

    /**
     * Build and return the final Paseto token string.
     *
     * @return string The encoded Paseto token.
     *
     * @throws PasetoException
     */
    public function getToken(): string
    {
        return $this->builder->toString();
    }
}
