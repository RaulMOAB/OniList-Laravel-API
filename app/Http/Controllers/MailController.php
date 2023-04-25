<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\MailNotify;
use Illuminate\Support\Str;
use App\Models\Verify;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MailController extends Controller
{
    public function index($email) 
    {
        // check if the user exists in users table
        $count = User::where('email', $email)->count();

        if ($count > 0) {
            return response()->json([
                'message' => 'This account has been created',
                'status'  => 'failed',
            ]);
        }

        // generate random code
        $code = rand(100000,999999);

        // email body
        $data = [
            'subject' => 'Onilist Mail',
            'body'    => 'Hello This is my email delivery',
            'code'    => $code,
        ];

        $verify_if_mail_exist = Verify::where('email',$email)->first();

        // if email does not exist in DB create new row
        if ($verify_if_mail_exist == null) 
        {
            $create_verification = Verify::create([
                'email' => $email,
                'code'  => $code,
            ]);
        }
        //else update existing row
        else
        {
            Verify::where('email',$email)->update(['code' => $code,]);
        }

        // send mail
        try 
        {
            Mail::to($email)->send(new MailNotify($data));
            return response()->json([
                'message' => 'Check verification code in your mail box',
                'status'  => 'success',
            ]);

        } catch (Exception $th) 
        {
            return response()->json([
                'message' => 'Sorry something went wrong!',
                'status'  => 'failed',
            ]);
        }
    }

    static function verifyMail($email, $code) 
    {
        $verify_mail = Verify::where('email',$email)->get();

        $verify_code = $verify_mail[0]['code'];

        if($verify_code == $code)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
