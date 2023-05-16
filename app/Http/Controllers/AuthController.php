<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Verify;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\MailController;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);//?Devuelve el token
    

        // return response()->json([
        //     'token' => $token,
        //     'email' => $request['email'],
        //     'password' => $request['password'],
        //     'user' => $user
        // ]);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect credentials',
            ], 401);
        }
        
        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'auth' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ])->withCookie(cookie('token',$token,60));

    }

    public function register(Request $request){

        //TODO delete row in 1 hour (secondary)

        $verify_account = MailController::verifyMail($request->email, $request->code);

        if ($verify_account) 
        {
            $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', Password::min(8)
                ->mixedCase() // Uppercase and Lowercase
                ->letters()   // Letters
                ->numbers()   // Number
                ->symbols(),  // Character Non-alphanumeric
                    ],
            ]);
    
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => "registered_user",
                'profile_image'=>'default_profile.png',
                'banner_image'=>'default_banner.jpg'
            ]);

            $delete_verified_mail = Verify::where('email', $request->email)->delete();
    
            $credentials = $request->only('email', 'password');
            $myTTL = 90; //minutes
            JWTAuth::factory()->setTTL($myTTL);
            $token = JWTAuth::attempt($credentials);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user,
                'registered' => true,
                'auth' => [
                   'token' => $token,
                 'type' => 'bearer',
                ]
            ]);
        }
        else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid code',
                'registered' => false,
                'code' => $request->code,
            ]);
        }
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Logout successful'], 200);
        } catch (JWTException $exception) {
            return response()->json(['message' => 'Unable to logout'], 500);
        }
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

}
