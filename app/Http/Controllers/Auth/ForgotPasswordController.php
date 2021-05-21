<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Mail\WelcomeEmail;
use App\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Mail;

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

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        $user = User::where('email', '=', $request->email)
            ->where('is_enabled', '=', true)
            ->first();

        if ($user) {
            $response = Password::RESET_LINK_SENT;
            Mail::to($user)->send(new WelcomeEmail($user));
            return back()->with('status', trans($response));
        } else {
            $response = Password::INVALID_USER;
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans($response)]);
        }

    }
}
