<?php

declare(strict_types=1);

namespace MyDaniel\Paseto;

use MyDaniel\Paseto\Contracts\PasetoTokenParserContract;
use ParagonIE\Paseto\Exception\InvalidVersionException;
use ParagonIE\Paseto\Exception\PasetoException;
use ParagonIE\Paseto\JsonToken;
use ParagonIE\Paseto\Keys\Base\SymmetricKey;
use ParagonIE\Paseto\Parser;
use ParagonIE\Paseto\ProtocolCollection;
use ParagonIE\Paseto\Purpose;
use ParagonIE\Paseto\Rules\ForAudience;
use ParagonIE\Paseto\Rules\IssuedBy;
use ParagonIE\Paseto\Rules\NotExpired;
use ParagonIE\Paseto\Rules\ValidAt;

/**
 * Parses and validates a Paseto token.
 *
 * This class configures and uses the Paseto parser to verify the authenticity
 * and validity of a given token string according to a set of predefined rules.
 */
final class PasetoTokenParser implements PasetoTokenParserContract
{
    /**
     * The underlying Paseto parser instance from the paragonie/paseto library.
     *
     * @var Parser
     */
    private Parser $parser;

    /**
     * Create a new PasetoTokenParser instance.
     *
     * Sets up the parser with the shared key and default validation rules
     * (e.g., must be v4 local, not expired, and valid at the current time).
     *
     * @param  SymmetricKey  $sharedKey  The symmetric key for decryption.
     *
     * @throws PasetoException
     * @throws InvalidVersionException
     */
    public function __construct(SymmetricKey $sharedKey)
    {
        $this->parser = (new Parser())
            ->setKey($sharedKey)
            ->setAllowedVersions(ProtocolCollection::v4())
            ->setPurpose(Purpose::local())
            ->addRule(new ValidAt())
            ->addRule(new NotExpired());
    }

    /**
     * Add a validation rule to check the token's issuer (iss).
     *
     * @param  string  $issuer  The expected issuer.
     *
     * @return self
     */
    public function setIssuedBy(string $issuer): self
    {
        $this->parser->addRule(new IssuedBy($issuer));

        return $this;
    }

    /**
     * Add a validation rule to check the token's audience (aud).
     *
     * @param  string  $audience  The expected audience.
     *
     * @return self
     */
    public function setForAudience(string $audience): self
    {
        $this->parser->addRule(new ForAudience($audience));

        return $this;
    }

    /**
     * Parse and validate the given token string.
     *
     * @param  string  $token  The encoded Paseto token.
     *
     * @return JsonToken The parsed and validated token object.
     *
     * @throws PasetoException If the token is invalid, expired, or fails any validation rule.
     */
    public function parse(string $token): JsonToken
    {
        return $this->parser->parse($token);
    }
}
