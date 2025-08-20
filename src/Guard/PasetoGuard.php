<?php

declare(strict_types=1);

namespace MyDaniel\Paseto\Guard;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use MyDaniel\Paseto\Contracts\BlacklistContract;
use MyDaniel\Paseto\Contracts\PasetoTokenParserContract;
use MyDaniel\Paseto\Events\TokenAuthenticated;
use MyDaniel\Paseto\Events\TokenRevoked;
use MyDaniel\Paseto\Exceptions\TokenBlacklistedException;
use MyDaniel\Paseto\Exceptions\TokenExpiredException;
use MyDaniel\Paseto\Exceptions\TokenInvalidException;
use ParagonIE\Paseto\Exception\PasetoException as ParagonPasetoException;

/**
 * Paseto authentication guard for Laravel.
 */
class PasetoGuard implements Guard
{
    use GuardHelpers;

    /**
     * The payload of the last token attempt.
     *
     * @var array|null
     */
    protected ?array $lastAttemptedPayload = null;

    /**
     * PasetoGuard constructor.
     *
     * @param  UserProvider  $provider
     * @param  PasetoTokenParserContract  $parser
     * @param  BlacklistContract  $blacklist
     */
    public function __construct(
        UserProvider $provider,
        protected PasetoTokenParserContract $parser,
        protected BlacklistContract $blacklist
    ) {
        $this->setProvider($provider);
    }

    /**
     * Log the user out of the application and blacklist the token.
     *
     * @return void
     *
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     */
    public function logout(): void
    {
        $payload = $this->getTokenPayload();

        if ($payload && $this->blacklist->add($payload)) {
            TokenRevoked::dispatch($payload);
        }

        $this->user = null;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     *
     * @throws TokenBlacklistedException
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     */
    public function user(): ?Authenticatable
    {
        if ($this->hasUser() && !app()->runningUnitTests()) {
            return $this->user;
        }

        $payload = $this->getTokenPayload();

        if (!$payload) {
            return null;
        }

        if ($this->blacklist->has($payload)) {
            throw new TokenBlacklistedException();
        }

        $this->user = $this->getProvider()->retrieveById($payload['sub']);

        if ($this->user) {
            TokenAuthenticated::dispatch($this->user);
        }

        return $this->user;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        return !empty($this->attempt($credentials));
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     *
     * @return Authenticatable|null
     */
    public function attempt(array $credentials = []): ?Authenticatable
    {
        $provider = $this->getProvider();

        $this->user = $provider->retrieveByCredentials($credentials);
        $this->user = $this->user && $provider->validateCredentials($this->user, $credentials) ? $this->user : null;

        return $this->user;
    }

    /**
     * Get the token payload from the current request.
     *
     * @return array|null
     *
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     */
    public function getTokenPayload(): ?array
    {
        if ($this->lastAttemptedPayload) {
            return $this->lastAttemptedPayload;
        }

        $token = $this->getTokenFromRequest();

        if (!$token) {
            return null;
        }

        try {
            return $this->lastAttemptedPayload = $this->parser->parse($token)->getClaims();
        } catch (ParagonPasetoException $e) {
            if (str_contains($e->getMessage(), 'expired')) {
                throw new TokenExpiredException();
            }

            throw new TokenInvalidException();
        }
    }

    /**
     * Get the token from the request.
     *
     * @return string|null
     */
    private function getTokenFromRequest(): ?string
    {
        $request = request();

        return $request->bearerToken() ?? $request->token;
    }
}
