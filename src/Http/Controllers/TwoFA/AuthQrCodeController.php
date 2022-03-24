<?php

namespace TwoFA\Http\Controllers\TwoFA;


use TwoFA\Http\Controllers\Controller;

class AuthQrCodeController extends Controller
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        if (!$this->user->is2FA()) {
            $this->user->enable2fa();
        }

        return $this->user->twoFactorQrCodeSvg();
    }

}
