<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Verify;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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


        //$token = Auth::attempt($credentials);

        $myTTL = 90; //minutes
        JWTAuth::factory()->setTTL($myTTL);
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
            ]);

    }

    public function register(Request $request){

        $count = User::where('email', $request->email)->count();

        if ($count > 0) {
            return response()->json(['This account has been created']);
        }

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
                'auth' => [
                   'token' => $token,
                 'type' => 'bearer',
                ]
            ]);
        }
        else{
            return response()->json(['Codigo incorrecto']);
        }
    }

    public function logout()
    {
        Auth::logout();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
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
