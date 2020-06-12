<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Traits\UserSendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use UserSendsPasswordResetEmails;
}
