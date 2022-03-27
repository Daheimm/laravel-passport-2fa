<?php

namespace LP\TwoFA;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LP\TwoFA\Contracts\TwoFaAuthInterface;

trait TwoFactorAuthentication
{
    /**
     * @return string
     */
    public function twoFactorQrCodeSvg(): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd()
            )
        ))->writeString($this->twoFactorQrCodeUrl());
        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * @return mixed
     */
    public function twoFactorQrCodeUrl(): mixed
    {
        return app(TwoFaAuthInterface::class)->qrCodeUrl(
            config('app.name'),
            $this->name,
            decrypt($this->google2fa_secret),
        );
    }

    /**
     * @return mixed
     */
    public function recoveryCodes(): mixed
    {
        if ($this->google2fa_recovery_codes) {
            return json_decode(decrypt($this->google2fa_recovery_codes), true);
        }
        return [];
    }

    /**
     * @return bool
     */
    public function enable2fa(): bool
    {

        return $this->update(
            [
                'google2fa_secret' => encrypt(app(TwoFaAuthInterface::class)->generateSecretKey()),
                "google2fa_recovery_codes" => encrypt(
                    json_encode(
                        Collection::times(6, function () {
                            return
                                [
                                    "code" => Str::random(10) . '-' . Str::random(10),
                                    "active" => true,
                                ];
                        })->all()
                    )
                ),
            ]
        );
    }

    /**
     * @return bool
     */
    public function disable2fa(): bool
    {
        return $this->update(
            [
                'google2fa_secret' => null,
                "google2fa_enable" => false,
                "google2fa_recovery_codes" => null,
            ]
        );
    }

    /**
     * @return bool
     */
    public function is2FA(): bool
    {
        return !is_null($this->google2fa_secret) &&
            !is_null($this->google2fa_recovery_codes) &&
            $this->google2fa_enable;
    }

    public function eventError($request, string $error)
    {

    }

    /**
     * @param  string  $code
     * @return void
     */
    public function replaceRecoveryCode(string $code): void
    {
        $updatedKeys = collect(json_decode(decrypt($this->google2fa_recovery_codes), true))
            ->transform(function ($item) use ($code) {
                if ($item['code'] === $code) {
                    $item['active'] = false;
                }
                return $item;
            })->toJson();

        $this->forceFill(
            [
                'google2fa_recovery_codes' => encrypt($updatedKeys),
            ]
        )->save();
    }

    /**
     * Number of keys used
     * @return mixed
     */
    public function isUsedAllKeys(): mixed
    {
        return collect(json_decode(decrypt($this->google2fa_recovery_codes), true))->sum('active');
    }
}
