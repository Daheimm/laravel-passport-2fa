<?php

namespace TwoFA\Http\Controllers\TwoFA;


use Illuminate\Http\JsonResponse;
use TwoFA\Http\Controllers\Controller;


class RecoveryCodeController extends Controller
{
    /**
     * @return array|JsonResponse
     */
    public function index(): array|JsonResponse
    {
        if (!$this->user->is2FA()) {
            return [];
        }

        return response()->json(
            json_decode(decrypt($this->user->google2fa_recovery_codes), true)
        );
    }

}
