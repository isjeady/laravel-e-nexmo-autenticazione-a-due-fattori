<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Nexmo\Client\Exception\Exception;
use Nexmo\Laravel\Facade\Nexmo;

class SmsController extends Controller{

    public function show(Request $request) {
        return view('auth.sms.verify');
    }

    public function resend_sms(Request $request) {
        $phone_number = $request->session()->get('verify:phone_number');
        return redirect()->back()->with('resend_sms', 'Resend success:' . $phone_number);
    }

    public function verify(Request $request) {
        logger("VERIFY");
        $this->validate($request, [
            'code' => 'size:4',
        ]);

        $user_id = $request->session()->get('verify:user:id');
        $user = User::where('id',$user_id)->get()->first();

        try {
            $response = Nexmo::verify()->check(
                $user->request_id,
                $request->code
            );
            //logger("response");
            //logger($response);
            Auth::loginUsingId($request->session()->pull('verify:user:id'));
            return redirect('/home');
        } catch (Exception $e) {
            return redirect()->back()->withErrors([
                'code' => $e->getMessage()
            ]);
        }
    }
}
