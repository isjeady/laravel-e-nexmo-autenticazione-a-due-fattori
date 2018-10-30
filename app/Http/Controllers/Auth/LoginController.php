<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Nexmo\Laravel\Facade\Nexmo;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticated(Request $request, Authenticatable $user){
        Auth::logout();
        $request->session()->put('verify:user:id', $user->id);
        $request->session()->put('verify:phone_number', $user->phone_number);

        try {
            $verification = Nexmo::verify()->start([
                'number' => $user->phone_number,
                'brand'  => 'Isjeady Sms'
            ]);
            $user->request_id = $verification->getRequestId();
            $user->save();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([
                'code' => $e->getMessage()
            ]);
        }

        /*
        logger("verification");
        logger($verification);
        */
        return redirect('verify');
    }
}
