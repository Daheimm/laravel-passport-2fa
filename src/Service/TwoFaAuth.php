<?php

namespace TwoFA\Service;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Cache\Repository;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use TwoFA\Contracts\TwoFaAuthInterface;

class TwoFaAuth implements TwoFaAuthInterface
{
    /**
     * @param Google2FA $engine
     * @param Repository $cache
     */
    public function __construct(
        protected Google2FA $engine,
        protected Repository $cache,
    ) {
    }

    /**
     * @return string
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function generateSecretKey(): string
    {
        return $this->engine->generateSecretKey();
    }

    /**
     * Get the two factor authentication QR code URL.
     *
     * @param string $companyName
     * @param string $companyEmail
     * @param string $secret
     * @return string
     */
    public function qrCodeUrl($companyName, $companyEmail, $secret): string
    {
        return $this->engine->getQRCodeUrl($companyName, $companyEmail, $secret);
    }

    /**
     * Verify the given code.
     *
     * @param string $secret
     * @param string $code
     * @return bool
     */
    public function verify($secret, $code): bool
    {
        $timestamp = $this->engine->verifyKeyNewer(
            $secret,
            $code,
            optional($this->cache)->get($key = '2fa_codes.' . md5($code))
        );

        if ($timestamp !== false) {
            optional($this->cache)->put($key, $timestamp, ($this->engine->getWindow() ?: 1) * 60);
            return true;
        }
        return false;
    }

    /**
     * @param string $recoveryCode
     * @return mixed
     */
    public function validRecoveryCode(string $recoveryCode, User|Authenticatable $user): mixed
    {
        return collect($user->recoveryCodes())->first(function ($code) use ($recoveryCode) {
            if (hash_equals($recoveryCode, $code['code']) && $code['active'] === true) {
                return true;
            }
            return false;
        });
    }
}
