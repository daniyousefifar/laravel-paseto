<?php

declare(strict_types=1);

namespace MyDaniel\Paseto\Traits;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use MyDaniel\Paseto\Events\TokenGenerated;
use MyDaniel\Paseto\Paseto;
use ParagonIE\Paseto\Exception\InvalidKeyException;
use ParagonIE\Paseto\Exception\InvalidPurposeException;
use ParagonIE\Paseto\Exception\PasetoException;

/**
 * Trait for models that can generate Paseto tokens.
 */
trait HasPaseto
{
    /**
     * Get the token identifier.
     *
     * @return string
     */
    public function getJwtId(): string
    {
        return Str::uuid()->toString();
    }

    /**
     * Get the subject identifier.
     *
     * @return string
     */
    public function getJwtSubjectId(): string
    {
        return $this->getKey();
    }

    /**
     * Get the 'not before' time for the token.
     *
     * @return Carbon|null
     */
    public function getJwtValidFromTime(): ?Carbon
    {
        return null;
    }

    /**
     * Get the expiration time for the token.
     *
     * @return Carbon|null
     */
    public function getJwtValidUntilTime(): ?Carbon
    {
        $expiration = config('paseto.expiration');

        return $expiration ? Carbon::now()->addMinutes($expiration) : null;
    }

    /**
     * Get the custom claims to be added to the token.
     *
     * @return array
     */
    public function getJwtCustomClaims(): array
    {
        return [];
    }

    /**
     * Generate a new Paseto token for the user.
     *
     * @param  array  $config  Custom configuration for token generation.
     *
     * @return string
     *
     * @throws InvalidPurposeException
     * @throws PasetoException
     * @throws InvalidKeyException
     * @throws Exception
     */
    public function generateToken(array $config = []): string
    {
        $nbf = $config['valid_from'] ?? $this->getJwtValidFromTime();
        $exp = $config['valid_until'] ?? $this->getJwtValidUntilTime();

        $paseto = app(Paseto::class);

        $token = $paseto
            ->builder()
            ->setIssuer(config('paseto.issuer'))
            ->setAudience(config('paseto.audience'))
            ->setSubject($this->getJwtSubjectId())
            ->setNotBefore($nbf ? $nbf->toDateTimeImmutable() : null)
            ->setExpiration($exp ? $exp->toDateTimeImmutable() : null)
            ->setJti($config['id'] ?? $this->getJwtId())
            ->setClaims(array_replace(config('paseto.claims'), $config['claims'] ?? $this->getJwtCustomClaims()))
            ->getToken();

        TokenGenerated::dispatch($this, $token);

        return $token;
    }
}
