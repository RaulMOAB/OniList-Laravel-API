<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\MailNotify;
use Illuminate\Support\Str;
use App\Models\Verify;
use Illuminate\Support\Facades\DB;

class MailController extends Controller
{
    public function index($email) 
    {
        $code = rand(100000,999999);

        $data = [
            'subject' => 'Onilist Mail',
            'body'    => 'Hello This is my email delivery',
            'code'    => $code,
        ];

        $verify_if_mail_exist = Verify::where('email',$email)->first();

        if ($verify_if_mail_exist == null) 
        {
            $create_verification = Verify::create([
                'email' => $email,
                'code'  => $code,
            ]);
        }
        else
        {
            Verify::where('email',$email)->update(['code' => $code,]);
        }

        try 
        {
            Mail::to($email)->send(new MailNotify($data));
            return response()->json([
                'message' => 'Check verification code in your mail box',
                'status' => 'success',
            ]);

        } catch (Exception $th) 
        {
            return response()->json(['Sorry something went wrong!']);
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
