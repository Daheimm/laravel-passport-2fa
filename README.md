Package to work with two-factor authentication along with laravel passport

In your Users model you should add

           use TwoFactorAuthentication


            php artisanvendor:publish --provider="TwoFA\Providers\TwoFaAppServiceProvider"
            php artisanvendor:publish --provider="TwoFA\Providers\TwoFaEventServiceProvider"

run the migrations they will add three fields to the users table,also add them to fillable

            'google2fa_secret',
            'google2fa_recovery_codes',
            'google2fa_enable',

You now have these routers available, routers are secured by default Route::middleware('auth:api')

            POST      two-factor-authentication 
            DELETE    two-factor-authentication/{user} 
            GET|HEAD  two-factor-qr-code 
            GET|HEAD  two-factor-recovery-codes
      

How it works

We always get a unique ik-svu until it is enabled. Enabled using a code obtained from the Google authorization of the application

            GET|HEAD  two-factor-qr-code 

Enables and disables 2FA, but backup codes can be used to disable it.

            POST      two-factor-authentication
            DELETE    two-factor-authentication/{user} 

List of backup codes, they have statuses for display on the frontend.

          GET|HEAD  two-factor-recovery-codes

            {
            "code": "DiAHiXyqsV-PZ2grE1huc",
            "active": false
            },
            {
            "code": "jqzDmcYYmA-xCQiU75dyJ",
            "active": true
            },


Now, when authorizing through a passport, you will have to add a code field to the request body.
This is the code from the Google application or the backup code.



This should work.
When an access request comes in, we check to see if 2AF is enabled.
Return if "status2FA" is enabled: true.

further in the request body you have to send

Example for auth url oauth/token

        "grant_type" : "password",
        "client_id" : "2",
        "client_secret" : "6wAGwcP98VT90S5biQZjREIq4udQ7EhmwNrUzBkV",
        "username": "amosciski@example.com",
        "password": "password",
        "scope": "",
        "code":"DiAHiXyqsV-PZ2grE1huc"

Then you can get a token for further work.








