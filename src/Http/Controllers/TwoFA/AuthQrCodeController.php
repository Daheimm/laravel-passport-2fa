<?php

namespace LP\TwoFA\Http\Controllers\TwoFA;


use LP\TwoFA\Http\Controllers\Controller;

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
