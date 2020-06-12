<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use  App\Http\Traits\UserLoginTrait;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use UserLoginTrait;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::SECONDHOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:user')->except('logout');
    }
}
