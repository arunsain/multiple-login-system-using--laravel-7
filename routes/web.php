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
