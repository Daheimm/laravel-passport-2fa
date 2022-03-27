<?php

namespace LP\TwoFA\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;


class Controller
{
    protected ?Authenticatable $user;

    /**
     * Get Authenticate Profile, use Token
     */

    public function __construct()
    {
        $this->user = auth("api")->user();
    }
}
