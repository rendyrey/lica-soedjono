<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\LoginLog;
use Auth;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
    protected $loginRoute = 'login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $request->session()->put('area_id', $request->area_id);

        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (auth()->attempt(array($fieldType => $input['email'], 'password' => $input['password']))) {
            if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Analis' || Auth::user()->role == 'Dokter') {
                $this->log($request);
                return redirect()->route('pre-analytics');
            } else {
                // $this->log($request);
                return redirect()->route('lica-viewer');
            }
        } else {
            return redirect()->route('login')
                ->with('error', "The credentials doesn't match our records.");
        }

        // $user = \App\User::where([
        //     'username' => $input['email'],
        //     'password' => $input['password']
        // ])->first();

        // if ($user) {
        //     $this->log($request);

        //     return redirect()->route('pre-analytics');
        // } else {
        //     return redirect()->route('login')
        //         ->with('error', "The credentials doesn't match our records.");
        // }
    }

    private function log($request)
    {
        $userId = Auth::user()->id;
        $username = Auth::user()->username;
        // delete the login log for all previous log
        LoginLog::where('user_id', $userId)->delete();

        // record the login
        LoginLog::create([
            'user_id' => $userId,
            'username' => $username,
            'last_login' => now(),
            'ip_address' => $request->ip(),
            'browser' => $request->header('User-Agent')
        ]);
    }
}
