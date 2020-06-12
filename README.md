						HOW TO CREATE  AUTHENTICATION USING  ADMIN TABLE



1. Create admin model amd admin Migration

		php artisan make:model Admin  -m


2. Make some change in database/migration of admin


		<?php

		use Illuminate\Database\Migrations\Migration;
		use Illuminate\Database\Schema\Blueprint;
		use Illuminate\Support\Facades\Schema;

		class CreateAdminsTable extends Migration
		{
		    /**
		     * Run the migrations.
		     *
		     * @return void
		     */
		    public function up()
		    {
		        Schema::create('admins', function (Blueprint $table) {
		            $table->id();
		            $table->string('name');
		            $table->string('email')->unique();
		            $table->timestamp('email_verified_at')->nullable();
		            $table->string('password');
		            $table->rememberToken();
		            $table->timestamps();
		        });
		    }

		    /**
		     * Reverse the migrations.
		     *
		     * @return void
		     */
		    public function down()
		    {
		        Schema::dropIfExists('admins');
		    }
		}







3. Past this code in Admin model class in App/Admin.php


		<?php

		namespace App;

		use Illuminate\Contracts\Auth\MustVerifyEmail;
		use Illuminate\Foundation\Auth\User as Authenticatable;
		use Illuminate\Notifications\Notifiable;

		class Admin extends Authenticatable
		{
		    use Notifiable;

		    /**
		     * The attributes that are mass assignable.
		     *
		     * @var array
		     */
		    protected $fillable = [
		        'name', 'email', 'password',
		    ];

		    /**
		     * The attributes that should be hidden for arrays.
		     *
		     * @var array
		     */
		    protected $hidden = [
		        'password', 'remember_token',
		    ];

		    /**
		     * The attributes that should be cast to native types.
		     *
		     * @var array
		     */
		    protected $casts = [
		        'email_verified_at' => 'datetime',
		    ];
		}




		


4. Update User.php by this code in App/User.php


		<?php

		namespace App;

		use Illuminate\Contracts\Auth\MustVerifyEmail;
		use Illuminate\Foundation\Auth\User as Authenticatable;
		use Illuminate\Notifications\Notifiable;
		use App\Notifications\ResetPasswordNotification;


		class User extends Authenticatable
		{
		    use Notifiable;





		    /**
		 * Send the password reset notification.
		 *
		 * @param  string  $token
		 * @return void
		 */
		public function sendPasswordResetNotification($token)
		{
		    $this->notify(new ResetPasswordNotification($token));
		}


		}



	


5. Make some Changes in App\Http\Controllers\Auth\RegisterController.php


		<?php

		namespace App\Http\Controllers\Auth;

		use App\Http\Controllers\Controller;
		use App\Providers\RouteServiceProvider;
		use App\Admin;
		use Illuminate\Foundation\Auth\RegistersUsers;
		use Illuminate\Support\Facades\Hash;
		use Illuminate\Support\Facades\Validator;

		class RegisterController extends Controller
		{
		    /*
		    |--------------------------------------------------------------------------
		    | Register Controller
		    |--------------------------------------------------------------------------
		    |
		    | This controller handles the registration of new users as well as their
		    | validation and creation. By default this controller uses a trait to
		    | provide this functionality without requiring any additional code.
		    |
		    */

		    use RegistersUsers;

		    /**
		     * Where to redirect users after registration.
		     *
		     * @var string
		     */
		    protected $redirectTo = RouteServiceProvider::HOME;

		    /**
		     * Create a new controller instance.
		     *
		     * @return void
		     */
		    public function __construct()
		    {
		        $this->middleware('guest');
		    }

		    /**
		     * Get a validator for an incoming registration request.
		     *
		     * @param  array  $data
		     * @return \Illuminate\Contracts\Validation\Validator
		     */
		    protected function validator(array $data)
		    {
		        return Validator::make($data, [
		            'name' => ['required', 'string', 'max:255'],
		            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
		            'password' => ['required', 'string', 'min:8', 'confirmed'],
		        ]);
		    }

		    /**
		     * Create a new user instance after a valid registration.
		     *
		     * @param  array  $data
		     * @return \App\User
		     */
		    protected function create(array $data)
		    {
		        return Admin::create([
		            'name' => $data['name'],
		            'email' => $data['email'],
		            'password' => Hash::make($data['password']),
		        ]);
		    }
		}




6. Make some changes in App\Http\Controllers\Auth\LoginController.php



		<?php

		namespace App\Http\Controllers\Auth;

		use App\Http\Controllers\Controller;
		use App\Providers\RouteServiceProvider;
		use Illuminate\Http\Request;
		use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

		     public function logout(Request $request)
		    {
		        //dd($request->all());
		        $this->guard()->logout();

		       // $request->session()->invalidate();

		       // $request->session()->regenerateToken();

		        if ($response = $this->loggedOut($request)) {
		            return $response;
		        }

		        return $request->wantsJson()
		            ? new Response('', 204)
		            : redirect('/');
		    }

		    use AuthenticatesUsers;

		    /**
		     * Where to redirect users after login.
		     *
		     * @var string
		     */
		    protected $redirectTo = RouteServiceProvider::HOME;

		    /**
		     * Create a new controller instance.
		     *
		     * @return void
		     */
		    public function __construct()
		    {
		        $this->middleware('guest')->except('logout');
		    }


		    // public function logout(Request $request)
		    // {
		    //     dd($request->all());
		    //     $this->guard()->logout();

		    //    // $request->session()->invalidate();

		    //     $request->session()->regenerateToken();

		    //     if ($response = $this->loggedOut($request)) {
		    //         return $response;
		    //     }

		    //     return $request->wantsJson()
		    //         ? new Response('', 204)
		    //         : redirect('/');
		    // }
		}


	



7. make Change in Config/auth.php
		

		<?php

		return [

		    /*
		    |--------------------------------------------------------------------------
		    | Authentication Defaults
		    |--------------------------------------------------------------------------
		    |
		    | This option controls the default authentication "guard" and password
		    | reset options for your application. You may change these defaults
		    | as required, but they're a perfect start for most applications.
		    |
		    */

		    'defaults' => [
		        'guard' => 'web',
		        'passwords' => 'admins',
		    ],

		    

		    /*
		    |--------------------------------------------------------------------------
		    | Authentication Guards
		    |--------------------------------------------------------------------------
		    |
		    | Next, you may define every authentication guard for your application.
		    | Of course, a great default configuration has been defined for you
		    | here which uses session storage and the Eloquent user provider.
		    |
		    | All authentication drivers have a user provider. This defines how the
		    | users are actually retrieved out of your database or other storage
		    | mechanisms used by this application to persist your user's data.
		    |
		    | Supported: "session", "token"
		    |
		    */

		    'guards' => [
		        'web' => [
		            'driver' => 'session',
		            'provider' => 'admins',
		        ],

		        'api' => [
		            'driver' => 'token',
		            'provider' => 'admins',
		            'hash' => false,
		        ],

		        'user' => [
		            'driver' => 'session',
		            'provider' => 'users',
		        ],
		    ],

		    /*
		    |--------------------------------------------------------------------------
		    | User Providers
		    |--------------------------------------------------------------------------
		    |
		    | All authentication drivers have a user provider. This defines how the
		    | users are actually retrieved out of your database or other storage
		    | mechanisms used by this application to persist your user's data.
		    |
		    | If you have multiple user tables or models you may configure multiple
		    | sources which represent each model / table. These sources may then
		    | be assigned to any extra authentication guards you have defined.
		    |
		    | Supported: "database", "eloquent"
		    |
		    */

		    'providers' => [
		        'admins' => [
		            'driver' => 'eloquent',
		            'model' => App\Admin::class,
		        ],
		         'users' => [
		            'driver' => 'eloquent',
		            'model' => App\User::class,
		        ],

		        // 'users' => [
		        //     'driver' => 'database',
		        //     'table' => 'users',
		        // ],
		    ],

		    /*
		    |--------------------------------------------------------------------------
		    | Resetting Passwords
		    |--------------------------------------------------------------------------
		    |
		    | You may specify multiple password reset configurations if you have more
		    | than one user table or model in the application and you want to have
		    | separate password reset settings based on the specific user types.
		    |
		    | The expire time is the number of minutes that the reset token should be
		    | considered valid. This security feature keeps tokens short-lived so
		    | they have less time to be guessed. You may change this as needed.
		    |
		    */

		    'passwords' => [
		        'admins' => [
		            'provider' => 'admins',
		            'table' => 'password_resets',
		            'expire' => 60,
		            'throttle' => 60,
		        ],
		         'users' => [
		            'provider' => 'users',
		            'table' => 'password_resets',
		            'expire' => 60,
		            'throttle' => 60,
		        ],
		    ],

		    /*
		    |--------------------------------------------------------------------------
		    | Password Confirmation Timeout
		    |--------------------------------------------------------------------------
		    |
		    | Here you may define the amount of seconds before a password confirmation
		    | times out and the user is prompted to re-enter their password via the
		    | confirmation screen. By default, the timeout lasts for three hours.
		    |
		    */

		    'password_timeout' => 10800,

		];


		
8. Make new  Folder at app/Http/Controller/	  

		User folder

9. Create New File in App\Http\Controllers\User\LoginController.php



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



10. Create New File in App\Http\Controllers\User\ResetPasswordController.php

		<?php

		namespace App\Http\Controllers\User;

		use App\Http\Controllers\Controller;
		use App\Providers\RouteServiceProvider;
		use App\Http\Traits\UserResetsPasswords;
		//use Illuminate\Foundation\Auth\ResetsPasswords;

		class ResetPasswordController extends Controller
		{
		    /*
		    |--------------------------------------------------------------------------
		    | Password Reset Controller
		    |--------------------------------------------------------------------------
		    |
		    | This controller is responsible for handling password reset requests
		    | and uses a simple trait to include this behavior. You're free to
		    | explore this trait and override any methods you wish to tweak.
		    |
		    */

		    use UserResetsPasswords;

		    /**
		     * Where to redirect users after resetting their password.
		     *
		     * @var string
		     */
		    protected $redirectTo = RouteServiceProvider::SECONDHOME;
		}


11. Create New File in App\Http\Controllers\User\ForgotPasswordController.php


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


12. Make new  Folder at app/Http/	  

		Traits folder

13. Create New File in App\Http\Traits\UserLoginTrait.php


		<?php

		namespace App\Http\Traits;
		use Illuminate\Http\Request;
		use Illuminate\Http\Response;
		use Illuminate\Support\Facades\Auth;
		use Illuminate\Validation\ValidationException;
		use Illuminate\Foundation\Auth\RedirectsUsers;
		use Illuminate\Foundation\Auth\ThrottlesLogins;



		trait UserLoginTrait
		{
		    use RedirectsUsers, ThrottlesLogins;

		    /**
		     * Show the application's login form.
		     *
		     * @return \Illuminate\View\View
		     */
		    public function showLoginForm()
		    {
		        return view('user.login');
		    }

		    /**
		     * Handle a login request to the application.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
		     *
		     * @throws \Illuminate\Validation\ValidationException
		     */
		    public function login(Request $request)
		    {
		        $this->validateLogin($request);

		        // If the class is using the ThrottlesLogins trait, we can automatically throttle
		        // the login attempts for this application. We'll key this by the username and
		        // the IP address of the client making these requests into this application.
		        if (method_exists($this, 'hasTooManyLoginAttempts') &&
		            $this->hasTooManyLoginAttempts($request)) {
		            $this->fireLockoutEvent($request);

		            return $this->sendLockoutResponse($request);
		        }

		        if ($this->attemptLogin($request)) {
		            return $this->sendLoginResponse($request);
		        }

		        // If the login attempt was unsuccessful we will increment the number of attempts
		        // to login and redirect the user back to the login form. Of course, when this
		        // user surpasses their maximum number of attempts they will get locked out.
		        $this->incrementLoginAttempts($request);

		        return $this->sendFailedLoginResponse($request);
		    }

		    /**
		     * Validate the user login request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return void
		     *
		     * @throws \Illuminate\Validation\ValidationException
		     */
		    protected function validateLogin(Request $request)
		    {
		        $request->validate([
		            $this->username() => 'required|string',
		            'password' => 'required|string',
		        ]);
		    }

		    /**
		     * Attempt to log the user into the application.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return bool
		     */
		    protected function attemptLogin(Request $request)
		    {
		        return $this->guard()->attempt(
		            $this->credentials($request), $request->filled('remember')
		        );
		    }

		    /**
		     * Get the needed authorization credentials from the request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return array
		     */
		    protected function credentials(Request $request)
		    {
		        return $request->only($this->username(), 'password');
		    }

		    /**
		     * Send the response after the user was authenticated.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return \Illuminate\Http\Response
		     */
		    protected function sendLoginResponse(Request $request)
		    {
		        $request->session()->regenerate();

		        $this->clearLoginAttempts($request);

		        if ($response = $this->authenticated($request, $this->guard()->user())) {
		            return $response;
		        }

		        return $request->wantsJson()
		                    ? new Response('', 204)
		                    : redirect()->intended($this->redirectPath());
		    }

		    /**
		     * The user has been authenticated.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @param  mixed  $user
		     * @return mixed
		     */
		    protected function authenticated(Request $request, $user)
		    {
		        //
		    }

		    /**
		     * Get the failed login response instance.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return \Symfony\Component\HttpFoundation\Response
		     *
		     * @throws \Illuminate\Validation\ValidationException
		     */
		    protected function sendFailedLoginResponse(Request $request)
		    {
		        throw ValidationException::withMessages([
		            $this->username() => [trans('auth.failed')],
		        ]);
		    }

		    /**
		     * Get the login username to be used by the controller.
		     *
		     * @return string
		     */
		    public function username()
		    {
		        return 'email';
		    }

		    /**
		     * Log the user out of the application.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return \Illuminate\Http\Response
		     */
		    public function logout(Request $request)
		    {

		       // dd($request->all());
		        $this->guard()->logout();

		       // $request->session()->invalidate();

		      //  $request->session()->regenerateToken();

		        if ($response = $this->loggedOut($request)) {
		            return $response;
		        }

		        return $request->wantsJson()
		            ? new Response('', 204)
		            : redirect('/');
		    }

		    /**
		     * The user has logged out of the application.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return mixed
		     */
		    protected function loggedOut(Request $request)
		    {
		        //
		    }

		    /**
		     * Get the guard to be used during authentication.
		     *
		     * @return \Illuminate\Contracts\Auth\StatefulGuard
		     */
		    protected function guard()
		    {
		        return Auth::guard('user');
		    }
		}


14. Create New File in App\Http\Traits\UserResetsPasswords.php


		<?php

		namespace App\Http\Traits;

		use Illuminate\Auth\Events\PasswordReset;
		use Illuminate\Http\JsonResponse;
		use Illuminate\Http\Request;
		use Illuminate\Support\Facades\Auth;
		use Illuminate\Support\Facades\Hash;
		use Illuminate\Support\Facades\Password;
		use Illuminate\Support\Str;
		use Illuminate\Validation\ValidationException;
		use Illuminate\Foundation\Auth\RedirectsUsers;

		trait UserResetsPasswords
		{
		    use RedirectsUsers;

		    /**
		     * Display the password reset view for the given token.
		     *
		     * If no token is present, display the link request form.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @param  string|null  $token
		     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
		     */
		    public function showResetForm(Request $request, $token = null)
		    {
		        return view('user.passwords.reset')->with(
		            ['token' => $token, 'email' => $request->email]
		        );
		    }

		    /**
		     * Reset the given user's password.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
		     */
		    public function reset(Request $request)
		    {
		        $request->validate($this->rules(), $this->validationErrorMessages());

		        // Here we will attempt to reset the user's password. If it is successful we
		        // will update the password on an actual user model and persist it to the
		        // database. Otherwise we will parse the error and return the response.
		        $response = $this->broker()->reset(
		            $this->credentials($request), function ($user, $password) {
		                $this->resetPassword($user, $password);
		            }
		        );

		        // If the password was successfully reset, we will redirect the user back to
		        // the application's home authenticated view. If there is an error we can
		        // redirect them back to where they came from with their error message.
		        return $response == Password::PASSWORD_RESET
		                    ? $this->sendResetResponse($request, $response)
		                    : $this->sendResetFailedResponse($request, $response);
		    }

		    /**
		     * Get the password reset validation rules.
		     *
		     * @return array
		     */
		    protected function rules()
		    {
		        return [
		            'token' => 'required',
		            'email' => 'required|email',
		            'password' => 'required|confirmed|min:8',
		        ];
		    }

		    /**
		     * Get the password reset validation error messages.
		     *
		     * @return array
		     */
		    protected function validationErrorMessages()
		    {
		        return [];
		    }

		    /**
		     * Get the password reset credentials from the request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return array
		     */
		    protected function credentials(Request $request)
		    {
		        return $request->only(
		            'email', 'password', 'password_confirmation', 'token'
		        );
		    }

		    /**
		     * Reset the given user's password.
		     *
		     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
		     * @param  string  $password
		     * @return void
		     */
		    protected function resetPassword($user, $password)
		    {
		        $this->setUserPassword($user, $password);

		        $user->setRememberToken(Str::random(60));

		        $user->save();

		        event(new PasswordReset($user));

		        $this->guard()->login($user);
		    }

		    /**
		     * Set the user's password.
		     *
		     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
		     * @param  string  $password
		     * @return void
		     */
		    protected function setUserPassword($user, $password)
		    {
		        $user->password = Hash::make($password);
		    }

		    /**
		     * Get the response for a successful password reset.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @param  string  $response
		     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
		     */
		    protected function sendResetResponse(Request $request, $response)
		    {
		        if ($request->wantsJson()) {
		            return new JsonResponse(['message' => trans($response)], 200);
		        }

		        return redirect($this->redirectPath())
		                            ->with('status', trans($response));
		    }

		    /**
		     * Get the response for a failed password reset.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @param  string  $response
		     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
		     */
		    protected function sendResetFailedResponse(Request $request, $response)
		    {
		        if ($request->wantsJson()) {
		            throw ValidationException::withMessages([
		                'email' => [trans($response)],
		            ]);
		        }

		        return redirect()->back()
		                    ->withInput($request->only('email'))
		                    ->withErrors(['email' => trans($response)]);
		    }

		    /**
		     * Get the broker to be used during password reset.
		     *
		     * @return \Illuminate\Contracts\Auth\PasswordBroker
		     */
		    public function broker()
		    {
		        return Password::broker('users');
		    }

		    /**
		     * Get the guard to be used during password reset.
		     *
		     * @return \Illuminate\Contracts\Auth\StatefulGuard
		     */
		    protected function guard()
		    {
		        return Auth::guard('user');
		    }
		}


15. Create New File in App\Http\Traits\UserSendsPasswordResetEmails.php


		<?php

		namespace App\Http\Traits;

		use Illuminate\Http\JsonResponse;
		use Illuminate\Http\Request;
		use Illuminate\Support\Facades\Password;
		use Illuminate\Validation\ValidationException;

		trait UserSendsPasswordResetEmails
		{
		    /**
		     * Display the form to request a password reset link.
		     *
		     * @return \Illuminate\View\View
		     */
		    public function showLinkRequestForm()
		    {
		        return view('user.passwords.email');
		    }

		    /**
		     * Send a reset link to the given user.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
		     */
		    public function sendResetLinkEmail(Request $request)
		    {
		        $this->validateEmail($request);

		        // We will send the password reset link to this user. Once we have attempted
		        // to send the link, we will examine the response then see the message we
		        // need to show to the user. Finally, we'll send out a proper response.
		        $response = $this->broker()->sendResetLink(
		            $this->credentials($request)
		        );

		        return $response == Password::RESET_LINK_SENT
		                    ? $this->sendResetLinkResponse($request, $response)
		                    : $this->sendResetLinkFailedResponse($request, $response);
		    }

		    /**
		     * Validate the email for the given request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return void
		     */
		    protected function validateEmail(Request $request)
		    {
		        $request->validate(['email' => 'required|email']);
		    }

		    /**
		     * Get the needed authentication credentials from the request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @return array
		     */
		    protected function credentials(Request $request)
		    {
		        return $request->only('email');
		    }

		    /**
		     * Get the response for a successful password reset link.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @param  string  $response
		     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
		     */
		    protected function sendResetLinkResponse(Request $request, $response)
		    {
		        return $request->wantsJson()
		                    ? new JsonResponse(['message' => trans($response)], 200)
		                    : back()->with('status', trans($response));
		    }

		    /**
		     * Get the response for a failed password reset link.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @param  string  $response
		     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
		     */
		    protected function sendResetLinkFailedResponse(Request $request, $response)
		    {
		        if ($request->wantsJson()) {
		            throw ValidationException::withMessages([
		                'email' => [trans($response)],
		            ]);
		        }

		        return back()
		                ->withInput($request->only('email'))
		                ->withErrors(['email' => trans($response)]);
		    }

		    /**
		     * Get the broker to be used during password reset.
		     *
		     * @return \Illuminate\Contracts\Auth\PasswordBroker
		     */
		    public function broker()
		    {
		        return Password::broker('users');
		    }
		}


16. create notification  by using this command 

		php artisan make:notification ResetPasswordNotification


17. make some changes in   App\Notifications\ResetPasswordNotification.php


		<?php

		namespace App\Notifications;

		use Illuminate\Bus\Queueable;
		use Illuminate\Contracts\Queue\ShouldQueue;
		use Illuminate\Notifications\Messages\MailMessage;
		use Illuminate\Notifications\Notification;
		use Illuminate\Support\Facades\Lang;

		class ResetPasswordNotification extends Notification
		{
		    use Queueable;
		    public $token;

		    /**
		     * Create a new notification instance.
		     *
		     * @return void
		     */
		    public function __construct($token)
		    {
		        //
		        $this->token = $token;
		    }

		    /**
		     * Get the notification's delivery channels.
		     *
		     * @param  mixed  $notifiable
		     * @return array
		     */
		    public function via($notifiable)
		    {
		        return ['mail'];
		    }

		    /**
		     * Get the mail representation of the notification.
		     *
		     * @param  mixed  $notifiable
		     * @return \Illuminate\Notifications\Messages\MailMessage
		     */
		     public function toMail($notifiable)
		    {
		        // if (static::$toMailCallback) {
		        //     return call_user_func(static::$toMailCallback, $notifiable, $this->token);
		        // }

		        // if (static::$createUrlCallback) {
		        //     $url = call_user_func(static::$createUrlCallback, $notifiable, $this->token);
		        // } else {
		            $url = url(route('user.password.reset', [
		                'token' => $this->token,
		                'email' => $notifiable->getEmailForPasswordReset(),
		            ], false));
		      //  }

		        return (new MailMessage)
		            ->subject(Lang::get('Reset Password Notification'))
		            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
		            ->action(Lang::get('Reset Password'), $url)
		            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
		            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
		    }

		    /**
		     * Get the array representation of the notification.
		     *
		     * @param  mixed  $notifiable
		     * @return array
		     */
		    public function toArray($notifiable)
		    {
		        return [
		            //
		        ];
		    }
		}



18. Make some Changes in App\Providers\RouteServiceProvider; 


		<?php

		namespace App\Providers;

		use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
		use Illuminate\Support\Facades\Route;

		class RouteServiceProvider extends ServiceProvider
		{
		    /**
		     * This namespace is applied to your controller routes.
		     *
		     * In addition, it is set as the URL generator's root namespace.
		     *
		     * @var string
		     */
		    protected $namespace = 'App\Http\Controllers';

		    /**
		     * The path to the "home" route for your application.
		     *
		     * @var string
		     */
		    public const HOME = '/home';
		    public const SECONDHOME = '/UserHome';
		    

		    /**
		     * Define your route model bindings, pattern filters, etc.
		     *
		     * @return void
		     */
		    public function boot()
		    {
		        //

		        parent::boot();
		    }

		    /**
		     * Define the routes for the application.
		     *
		     * @return void
		     */
		    public function map()
		    {
		        $this->mapApiRoutes();

		        $this->mapWebRoutes();

		        //
		    }

		    /**
		     * Define the "web" routes for the application.
		     *
		     * These routes all receive session state, CSRF protection, etc.
		     *
		     * @return void
		     */
		    protected function mapWebRoutes()
		    {
		        Route::middleware('web')
		            ->namespace($this->namespace)
		            ->group(base_path('routes/web.php'));
		    }

		    /**
		     * Define the "api" routes for the application.
		     *
		     * These routes are typically stateless.
		     *
		     * @return void
		     */
		    protected function mapApiRoutes()
		    {
		        Route::prefix('api')
		            ->middleware('api')
		            ->namespace($this->namespace)
		            ->group(base_path('routes/api.php'));
		    }
		}


19.  Make some change in App\Http\Middleware\RedirectIfAuthenticated.php

		
		<?php

		namespace App\Http\Middleware;

		use App\Providers\RouteServiceProvider;
		use Closure;
		use Illuminate\Support\Facades\Auth;

		class RedirectIfAuthenticated
		{
		    /**
		     * Handle an incoming request.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @param  \Closure  $next
		     * @param  string|null  $guard
		     * @return mixed
		     */
		    public function handle($request, Closure $next, $guard = null)
		    {


		        if (Auth::guard($guard)->check()) {

		            if($guard == 'user'){
		                return redirect(RouteServiceProvider::SECONDHOME);
		            }
		            return redirect(RouteServiceProvider::HOME);
		        }

		        return $next($request);
		    }
		}

20.  Make some change in App\Exceptions\Handler.php


		<?php

		namespace App\Exceptions;

		use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
		use Throwable;
		use Illuminate\Support\Arr;
		//use Exception;
		use Request;
		use Response;
		use App\Providers\RouteServiceProvider;
		use Illuminate\Auth\AuthenticationException;

		class Handler extends ExceptionHandler
		{
		    /**
		     * A list of the exception types that are not reported.
		     *
		     * @var array
		     */
		    protected $dontReport = [
		        //
		    ];

		    /**
		     * A list of the inputs that are never flashed for validation exceptions.
		     *
		     * @var array
		     */
		    protected $dontFlash = [
		        'password',
		        'password_confirmation',
		    ];

		    /**
		     * Report or log an exception.
		     *
		     * @param  \Throwable  $exception
		     * @return void
		     *
		     * @throws \Exception
		     */
		    public function report(Throwable $exception)
		    {
		        parent::report($exception);
		    }

		    /**
		     * Render an exception into an HTTP response.
		     *
		     * @param  \Illuminate\Http\Request  $request
		     * @param  \Throwable  $exception
		     * @return \Symfony\Component\HttpFoundation\Response
		     *
		     * @throws \Throwable
		     */
		    public function render($request, Throwable $exception)
		    {
		        return parent::render($request, $exception);
		    }

		    protected function unauthenticated($request, AuthenticationException $exception)
		    {
		        //        return $request->expectsJson()
		        //            ? response()->json(['message' => 'Unauthenticated.'], 401)
		        //            : redirect()->guest(route('login'));

		        if ($request->expectsJson()) {
		            return response()->json(['error' => 'Unauthenticated.'], 401);
		        }
		        $guard = Arr::get($exception->guards(),0);
		        switch ($guard) {
		            case 'user':
		                $login = 'user.login';
		                break;

		            default:
		                $login = 'login';
		                break;
		        }
		         return redirect()->guest(route($login));
		    } 

		}


20. create  new folder in resources/views/

		user folder

21. create new file in resources/views/user/login.blade.php

		@extends('layouts.app2')

		@section('content')
		<div class="container">
		    <div class="row justify-content-center">
		        <div class="col-md-8">
		            <div class="card">
		                <div class="card-header">{{ __('User Login') }}</div>

		                <div class="card-body">
		                    <form method="POST" action="{{ route('user.login') }}">
		                        @csrf

		                        <div class="form-group row">
		                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

		                            <div class="col-md-6">
		                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

		                                @error('email')
		                                    <span class="invalid-feedback" role="alert">
		                                        <strong>{{ $message }}</strong>
		                                    </span>
		                                @enderror
		                            </div>
		                        </div>

		                        <div class="form-group row">
		                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

		                            <div class="col-md-6">
		                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

		                                @error('password')
		                                    <span class="invalid-feedback" role="alert">
		                                        <strong>{{ $message }}</strong>
		                                    </span>
		                                @enderror
		                            </div>
		                        </div>

		                        <div class="form-group row">
		                            <div class="col-md-6 offset-md-4">
		                                <div class="form-check">
		                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

		                                    <label class="form-check-label" for="remember">
		                                        {{ __('Remember Me') }}
		                                    </label>
		                                </div>
		                            </div>
		                        </div>

		                        <div class="form-group row mb-0">
		                            <div class="col-md-8 offset-md-4">
		                                <button type="submit" class="btn btn-primary">
		                                    {{ __('Login') }}
		                                </button>

		                                @if (Route::has('password.request'))
		                                    <a class="btn btn-link" href="{{ route('user.password.request') }}">
		                                        {{ __('Forgot Your Password?') }}
		                                    </a>
		                                @endif
		                            </div>
		                        </div>
		                    </form>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		@endsection



22. create new file in resources/views/user/register.blade.php


		@extends('layouts.app2')

		@section('content')
		<div class="container">
		    <div class="row justify-content-center">
		        <div class="col-md-8">
		            <div class="card">
		                <div class="card-header">{{ __('Register') }}</div>

		                <div class="card-body">
		                    <form method="POST" action="{{ route('register') }}">
		                        @csrf

		                        <div class="form-group row">
		                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

		                            <div class="col-md-6">
		                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

		                                @error('name')
		                                    <span class="invalid-feedback" role="alert">
		                                        <strong>{{ $message }}</strong>
		                                    </span>
		                                @enderror
		                            </div>
		                        </div>

		                        <div class="form-group row">
		                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

		                            <div class="col-md-6">
		                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

		                                @error('email')
		                                    <span class="invalid-feedback" role="alert">
		                                        <strong>{{ $message }}</strong>
		                                    </span>
		                                @enderror
		                            </div>
		                        </div>

		                        <div class="form-group row">
		                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

		                            <div class="col-md-6">
		                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

		                                @error('password')
		                                    <span class="invalid-feedback" role="alert">
		                                        <strong>{{ $message }}</strong>
		                                    </span>
		                                @enderror
		                            </div>
		                        </div>

		                        <div class="form-group row">
		                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

		                            <div class="col-md-6">
		                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
		                            </div>
		                        </div>

		                        <div class="form-group row mb-0">
		                            <div class="col-md-6 offset-md-4">
		                                <button type="submit" class="btn btn-primary">
		                                    {{ __('Register') }}
		                                </button>
		                            </div>
		                        </div>
		                    </form>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		@endsection


23. create new FOLDER in resources/views/user

		passwords folder

24. create new file in resources/views/user/passwords/email.blade.php


		@extends('layouts.app2')

		@section('content')
		<div class="container">
		    <div class="row justify-content-center">
		        <div class="col-md-8">
		            <div class="card">
		                <div class="card-header">{{ __('User Reset Password') }}</div>

		                <div class="card-body">
		                    @if (session('status'))
		                        <div class="alert alert-success" role="alert">
		                            {{ session('status') }}
		                        </div>
		                    @endif

		                    <form method="POST" action="{{ route('user.password.email') }}">
		                        @csrf

		                        <div class="form-group row">
		                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

		                            <div class="col-md-6">
		                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

		                                @error('email')
		                                    <span class="invalid-feedback" role="alert">
		                                        <strong>{{ $message }}</strong>
		                                    </span>
		                                @enderror
		                            </div>
		                        </div>

		                        <div class="form-group row mb-0">
		                            <div class="col-md-6 offset-md-4">
		                                <button type="submit" class="btn btn-primary">
		                                    {{ __('Send Password Reset Link') }}
		                                </button>
		                            </div>
		                        </div>
		                    </form>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		@endsection


25. create new file in resources/views/user/passwords/reset.blade.php


		@extends('layouts.app2')

		@section('content')
		<div class="container">
		    <div class="row justify-content-center">
		        <div class="col-md-8">
		            <div class="card">
		                <div class="card-header">{{ __('Reset Password') }}</div>

		                <div class="card-body">
		                    <form method="POST" action="{{ route('user.password.update') }}">
		                        @csrf

		                        <input type="hidden" name="token" value="{{ $token }}">

		                        <div class="form-group row">
		                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

		                            <div class="col-md-6">
		                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

		                                @error('email')
		                                    <span class="invalid-feedback" role="alert">
		                                        <strong>{{ $message }}</strong>
		                                    </span>
		                                @enderror
		                            </div>
		                        </div>

		                        <div class="form-group row">
		                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

		                            <div class="col-md-6">
		                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

		                                @error('password')
		                                    <span class="invalid-feedback" role="alert">
		                                        <strong>{{ $message }}</strong>
		                                    </span>
		                                @enderror
		                            </div>
		                        </div>

		                        <div class="form-group row">
		                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

		                            <div class="col-md-6">
		                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
		                            </div>
		                        </div>

		                        <div class="form-group row mb-0">
		                            <div class="col-md-6 offset-md-4">
		                                <button type="submit" class="btn btn-primary">
		                                    {{ __('Reset Password') }}
		                                </button>
		                            </div>
		                        </div>
		                    </form>
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		@endsection

26.  create new file in resources/views/UserHome.blade.php

		@extends('layouts.app2')

		@section('content')
		<div class="container">
		    <div class="row justify-content-center">
		        <div class="col-md-8">
		            <div class="card">
		                <div class="card-header">User Dashboard</div>

		                <div class="card-body">
		                    @if (session('status'))
		                        <div class="alert alert-success" role="alert">
		                            {{ session('status') }}
		                        </div>
		                    @endif

		                    You are logged in!
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		@endsection


27.  Make some changes in  resources/views/home.blade.php

		@extends('layouts.app')

		@section('content')
		<div class="container">
		    <div class="row justify-content-center">
		        <div class="col-md-8">
		            <div class="card">
		                <div class="card-header">Admin Dashboard</div>

		                <div class="card-body">
		                    @if (session('status'))
		                        <div class="alert alert-success" role="alert">
		                            {{ session('status') }}
		                        </div>
		                    @endif

		                    You are logged in!
		                </div>
		            </div>
		        </div>
		    </div>
		</div>
		@endsection


28.  create new file in resources/views/layouts/app2.blade.php


		<!doctype html>
		<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
		<head>
		    <meta charset="utf-8">
		    <meta name="viewport" content="width=device-width, initial-scale=1">

		    <!-- CSRF Token -->
		    <meta name="csrf-token" content="{{ csrf_token() }}">

		    <title>{{ config('app.name', 'Laravel') }}</title>

		    <!-- Scripts -->
		    <script src="{{ asset('js/app.js') }}" defer></script>

		    <!-- Fonts -->
		    <link rel="dns-prefetch" href="//fonts.gstatic.com">
		    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

		    <!-- Styles -->
		    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
		</head>
		<body>
		    <div id="app">
		        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
		            <div class="container">
		                <a class="navbar-brand" href="{{ url('/') }}">
		                    {{ config('app.name', 'Laravel') }}
		                </a>
		                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
		                    <span class="navbar-toggler-icon"></span>
		                </button>

		                <div class="collapse navbar-collapse" id="navbarSupportedContent">
		                    <!-- Left Side Of Navbar -->
		                    <ul class="navbar-nav mr-auto">

		                    </ul>

		                    <!-- Right Side Of Navbar -->
		                    <ul class="navbar-nav ml-auto">
		                        <!-- Authentication Links -->
		                        @guest
		                            <li class="nav-item">
		                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
		                            </li>
		                            @if (Route::has('register'))
		                                <li class="nav-item">
		                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
		                                </li>
		                            @endif
		                        @else
		                            <li class="nav-item dropdown">
		                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
		                                    {{ Auth::user()->name }} <span class="caret"></span>
		                                </a>

		                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

		                                     
		                                    <a class="dropdown-item" href="{{ route('user.logout') }}"
		                                       onclick="event.preventDefault();
		                                                     document.getElementById('user-logout-form').submit();">
		                                        {{ __('User Logout') }}
		                                    </a>

		                                    <form id="user-logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
		                                        @csrf
		                                    </form>
		                                   
		                                </div>
		                            </li>
		                        @endguest
		                    </ul>
		                </div>
		            </div>
		        </nav>

		        <main class="py-4">
		            @yield('content')
		        </main>
		    </div>
		</body>
		</html>

29. Make some change in routes/web.php

		
		<?php

		use Illuminate\Support\Facades\Route;
		//use Session;

		/*
		|--------------------------------------------------------------------------
		| Web Routes
		|--------------------------------------------------------------------------
		|
		| Here is where you can register web routes for your application. These
		| routes are loaded by the RouteServiceProvider within a group which
		| contains the "web" middleware group. Now create something great!
		|
		*/

		Route::get('/', function () {
			 // $data = Session::all();
		  //       dump($data);
		    return view('welcome');
		});

		Auth::routes();

		Route::get('/home', 'HomeController@index')->name('home');
		Route::get('/UserHome', 'UserHomeController@index')->name('user.home');



		Route::prefix('user')->namespace('User')->group(function(){

		  Route::get('login', 'LoginController@showLoginForm')->name('user.loginPage');
		  Route::post('login', 'LoginController@login')->name('user.login');
		   Route::post('logout', 'LoginController@logout')->name('user.logout');


		  Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('user.password.request');
		  Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('user.password.email');
		  Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('user.password.reset');
		  Route::post('password/reset', 'ResetPasswordController@reset')->name('user.password.update');
		  


		});




