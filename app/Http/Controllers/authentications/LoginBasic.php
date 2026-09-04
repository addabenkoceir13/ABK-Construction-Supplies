<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginBasic extends Controller
{
  public function index()
  {
    return view('content.authentications.auth-login-basic');
  }

  public function login(Request $request){
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    $credentials = $request->only('email', 'password');
    $remember = $request->filled('remember');

    if (Auth::attempt($credentials, $remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/')
                    ->withSuccess(__('Signed in'));
    }

    return redirect("/auth/login-basic")
              ->withInput($request->only('email'))
              ->withErrors(['email' => __('Login details are not valid')])
              ->with('error', __('Login details are not valid'));
  }
}
