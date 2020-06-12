<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

class UserHomeController extends Controller
{
    //


     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:user');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //   $data = Session::all();
        // dump($data);
        return view('UserHome');
    }
}
