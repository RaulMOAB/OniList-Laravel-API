<?php

namespace App\Http\Controllers;

use App\Models\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use App\Mail\MailNotify;
use App\Models\Verify;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MailController extends Controller
{

    public function renewPassword(Request $request)
    {
        $code = $request->code;
        $token = $request->token;
        $password = $request->password;
        $attempts = $request->attempts;
        $email = MailController::verifyCode($code);




        $count = ForgotPassword::where('token', $token)->count();
        if ($count === 0) {
            return response()->json([
                'error' => 'Invalid token, please verify your email box or try the process again.'
            ]);
        } else {
            if ($attempts === 0) {
                Verify::where('code', $request->code)->delete();
                ForgotPassword::where('token', $token)->delete();
                return response()->json([
                    'error' => 'Too many attempts.'
                ]);
            }else{
                if($email){
                    $user = User::where('email',$email)->first();
                    $user->password = Hash::make($password);
                    $user->save();
                    Verify::where('code', $code)->delete();
                    ForgotPassword::where('token', $token)->delete();
                    return response()->json([
                        'success' => 'Your password has been updated successfully.'
                    ]);
                }else{
                    return response()->json([
                        'error' => 'Invalid code, please verify your email box.',
                        'invalid_attempt'=>'Invalid attempts'
                    ]);
                }
            }
        }



    }
    public function forgotPassword(Request $request)
    {
        $email = $request->email;
        $count = User::where('email', $email)->count();
        if ($count === 0) {
            return response()->json([
                'error' => 'This email is not registered in onilist.'
            ]);
        }
        $code = rand(100000, 999999);
        $token = Str::random(100);
        $url = "http://localhost:3000/recover-password/$token";

        // email body
        $data = [
            'subject' => 'Onilist Mail',
            'body' => 'Hello, to renew your password copy the code and click the next url:',
            'url' => $url,
            'code' => $code,
        ];
        $verify_if_mail_exist = Verify::where('email', $email)->first();

        // if email does not exist in DB create new row
        if ($verify_if_mail_exist == null) {
            $create_verification = Verify::create([
                'email' => $email,
                'code' => $code,
            ]);
        }
        //else update existing row
        else {
            Verify::where('email', $email)->update(['code' => $code,]);
        }
        // send mail
        try {
            ForgotPassword::create([
                'email' => $email,
                'token' => $token
            ]);
            Mail::to($email)->send(new MailNotify($data, 'emails.renew-password'));
            return response()->json([
                'success' => 'Check your mail box.'
            ]);

        } catch (Exception $th) {
            return response()->json([
                'error' => "Error while sending your email, please try later."
            ]);
        }

        //TODO Email verification
    }
    public function index($email)
    {
        // check if the user exists in users table
        $count = User::where('email', $email)->count();

        if ($count > 0) {
            return response()->json([
                'message' => 'This account has been created',
                'status' => 'failed',
            ]);
        }

        // generate random code
        $code = rand(100000, 999999);

        // email body
        $data = [
            'subject' => 'Onilist Mail',
            'body' => 'Hello This is my email delivery',
            'code' => $code,
        ];

        $verify_if_mail_exist = Verify::where('email', $email)->first();

        // if email does not exist in DB create new row
        if ($verify_if_mail_exist == null) {
            $create_verification = Verify::create([
                'email' => $email,
                'code' => $code,
            ]);
        }
        //else update existing row
        else {
            Verify::where('email', $email)->update(['code' => $code,]);
        }

        // send mail
        try {
            Mail::to($email)->send(new MailNotify($data));
            return response()->json([
                'message' => 'Check verification code in your mail box',
                'status' => 'success',
            ]);

        } catch (Exception $th) {
            return response()->json([
                'message' => 'Sorry something went wrong!',
                'status' => 'failed',
            ]);
        }
    }

    public function send(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $id = $request->id;
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'error' => 'User not found.',
            ]);
        }

        if (Hash::check($password, $user->password)) {
            // check if the user exists in users table
            $count = User::where('email', $email)->count();

            if ($count > 0) {
                return response()->json([
                    'error' => 'This email is already registered in Onilist.',
                ]);
            }

            // generate random code
            $code = rand(100000, 999999);

            // email body
            $data = [
                'subject' => 'Onilist',
                'body' => 'Hello This is my email delivery',
                'code' => $code,
            ];

            $verify_if_mail_exist = Verify::where('email', $email)->first();

            // if email does not exist in DB create new row
            if ($verify_if_mail_exist == null) {
                $create_verification = Verify::create([
                    'email' => $email,
                    'code' => $code,
                ]);
            }
            //else update existing row
            else {
                Verify::where('email', $email)->update(['code' => $code,]);
            }
            // send mail
            try {
                Mail::to($email)->send(new MailNotify($data));
                return response()->json([
                    'info' => 'Check verification code in your mail box',
                ]);
            } catch (Exception $th) {
                return response()->json([
                    'error' => 'Sorry something went wrong!',
                ]);
            }
        } else {
            return response()->json([
                'error' => 'Invalid password.',
            ]);
        }
    }

    static function verifyMail($email, $code)
    {
        $verify_mail = Verify::where('email', $email)->get();

        $verify_code = $verify_mail[0]['code'];

        if ($verify_code == $code) {
            return true;
        } else {
            return false;
        }
    }
        static function verifyCode($code)
    {
        $verify_code = Verify::where('code', $code)->first();

        if ($verify_code !== null) {
            return $verify_code->email;
        } else {
            return null;
        }
    }
}