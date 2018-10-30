<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmsController extends Controller{

    public function show(Request $request) {
        return view('auth.sms.verify');
    }

    public function verify(Request $request) {
        return 'Not Implemented';
    }
}
