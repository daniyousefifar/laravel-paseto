<?php

declare(strict_types=1);

namespace MyDaniel\Paseto;

use InvalidArgumentException;
use MyDaniel\Paseto\Contracts\PasetoTokenBuilderContract;
use MyDaniel\Paseto\Contracts\PasetoTokenParserContract;
use ParagonIE\Paseto\Exception\InvalidKeyException;
use ParagonIE\Paseto\Exception\InvalidPurposeException;
use ParagonIE\Paseto\Exception\InvalidVersionException;
use ParagonIE\Paseto\Exception\PasetoException;
use ParagonIE\Paseto\Keys\Base\SymmetricKey;
use ParagonIE\Paseto\Protocol\Version4;

/**
 * Main Paseto class for creating token builders and parsers.
 */
class Paseto
{
    /**
     * The symmetric key for token encryption and decryption.
     *
     * @var SymmetricKey
     */
    private SymmetricKey $sharedKey;

    /**
     * Paseto constructor.
     *
     * @param  string  $secretKey  The hex-encoded secret key.
     *
     * @throws PasetoException If the key is invalid.
     * @throws InvalidArgumentException If the key is not hex-encoded.
     */
    public function __construct(string $secretKey)
    {
        if (!ctype_xdigit($secretKey)) {
            throw new InvalidArgumentException("'Paseto Secret Key' must be a hex encoded key");
        }

        $decoded = hex2bin($secretKey);
        if ($decoded === false) {
            throw new InvalidArgumentException("Failed converting 'Paseto Secret Key' to binary");
        }

        $this->sharedKey = new SymmetricKey($decoded, new Version4());
    }

    /**
     * Get a new Paseto token builder instance.
     *
     * @return PasetoTokenBuilderContract
     *
     * @throws InvalidPurposeException
     * @throws InvalidKeyException
     * @throws PasetoException
     */
    public function builder(): PasetoTokenBuilderContract
    {
        return new PasetoTokenBuilder($this->sharedKey);
    }

    /**
     * Get a new Paseto token parser instance.
     *
     * @return PasetoTokenParserContract
     *
     * @throws PasetoException
     * @throws InvalidVersionException
     */
    public function parser(): PasetoTokenParserContract
    {
        return new PasetoTokenParser($this->sharedKey);
    }
}
