<?php

namespace TwoFA\Observers;


use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;
use TwoFA\Contracts\TwoFaAuthInterface;


class TokenObserver
{
    public function __construct(
        private TwoFaAuthInterface $authentication,
    ) {
    }

    public function creating(Token $authCode)
    {
        $request = request()->request;
        $user = User::find($authCode->user_id);

        if ($user->is2FA()) {
            $validator = Validator::make(
                request()->request->all(),
                [
                    "code" => "required",

                ]
            );

            if ($validator->fails()) {
                response()
                    ->json(["status2FA" => true])
                    ->header("Access-Control-Allow-Origin", "*")
                    ->send();
                exit();
            }


            if ($this->authentication->validRecoveryCode($request->get('code'), $user)) {
                $user->replaceRecoveryCode($request->get('code'));
            } elseif (!$this->authentication->verify(decrypt($user->google2fa_secret), $request->get('code'))) {
                $this->exit('The Code 2FA were incorrect.');
            }
        }
    }

    private function exit(string $message)
    {
        response()
            ->json(
                [
                    "error" => "2FA",
                    "error_description" => "The Code 2FA were incorrect.",
                    "message" => $message,
                ]
            )
            ->header("Access-Control-Allow-Origin", "*")
            ->send();
        exit();
    }
}
