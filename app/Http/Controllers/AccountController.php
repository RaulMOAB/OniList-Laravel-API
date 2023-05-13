<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Verify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\MailController;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    private const BANNER_PATH = 'public/banner/';
    private const PROFILE_PATH = 'public/profile/';

    private const DEFAULT_BANNER = 'default-banner.jpg';
    private const DEFAULT_PROFILE = 'default-profile.png';

    public function updateProfileImage(Request $request)
    {
        // Obtener la imagen enviada
        $image = $request->file('image');
        // Verificar si se envió una imagen
        if (!$image) {
            return response()->json(['error' => 'Error updating your image.'], 400);
        }
        $validation = $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg|max:3072',
        ]);
        if (!$validation) {
            return response()->json(['error' => 'Format not allowed or file size limit exceeded'], 400);
        }

        $id = $request->id;
        $user = User::find($id);
        $type = $request->type;
        $filename_sufix = $user->username;
        $filename = $user->username . '-'. time() . '.' . $image->getClientOriginalExtension();
        $all_files = glob(storage_path("app/public/$type/*"));

        foreach ($all_files as $file) {
            if (str_contains($file, $filename_sufix)) {
                if(!(str_contains($file, self::DEFAULT_PROFILE) || str_contains($file , self::DEFAULT_BANNER))){
                    File::delete($file);
                }
            }
        }
        $image->storeAs("/public/$type", $filename);

        if ($type === "profile") {
            $user->profile_image = $filename;
        } else if ($type === "banner") {
            $user->banner_image = $filename;
        }
        // Guardar la imagen en la base de datos
        $user->save();

        return response()->json(['success' => "Your $type image has been updated"]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['message' => "User not found"], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        $user = User::find($id);
        $password = $request->password;
        if(!$user){
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }
        if(Hash::check($password, $user->password)){
            if($user->profile_image !== self::DEFAULT_PROFILE ){
                Storage::delete(self::PROFILE_PATH.$user->profile_image);
            }
            if($user->banner_image !== self::DEFAULT_BANNER){
                Storage::delete(self::BANNER_PATH.$user->banner_image);
            }
            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'User was successfully deleted'
            ], 200);
        }else{
            return response()->json([
                'error'=> 'Invalid password.'
            ], 404);
        }


    }

    public function updateDescription(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        $user->description = $request->description;
        $saved = $user->save();

        if (!$saved) {
            return response()->json(["error" => "Error ocurred when saving user"]);
        }
        return response()->json(["success" => "Your description has been updated"]);

    }
    public function updateEmail(Request $request)
    {
        $id = $request->id;
        $email = $request->email;
        $verify_account = MailController::verifyMail($request->email, $request->code);
        if($verify_account){
            $user = User::find($id);
            $user->email = $email;
            $user->save();
            Verify::where('email', $email)->delete();
            return response()->json([
                'success' => 'Email updated successfully.'
            ]);
        }else{
            return response()->json([
                'error' => 'Invalid code verification'
            ]);
        }
    }
    public function updatePassword(Request $request){
        $id = $request->id;
        $new_password = $request->new_password;
        $last_password = $request->last_password;

        $user = User::find($id);

        if(!$user){
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }
        if(Hash::check($last_password, $user->password)){
            $hashed_new_password = Hash::make($new_password);
            $user->password = $hashed_new_password;
            $user->save();
            return response()->json([
                'success' => 'Password has been updated successfully.'
            ], 404);
        }else{
            return response()->json([
                'error' => 'Invalid password.'
            ], 404);
        }
    }
    public function updateUsername(Request $request)
    {
        $id = $request->id;
        $password = $request->password;
        $user = User::find($id);
        $last_username = $user->username;
        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }
        if(Hash::check($password, $user->password)){
            $user->username = $request->username;

            try {
                if($user->profile_image !== self::DEFAULT_PROFILE ){
                    $actual_profile_image_filename = $user->profile_image;
                    $extension = pathinfo($actual_profile_image_filename, PATHINFO_EXTENSION);
                    $actual_profile_image_filename_without_extension = pathinfo($actual_profile_image_filename, PATHINFO_FILENAME);
                    $new_profile_image_filename = str_replace($last_username, $request->username, $actual_profile_image_filename_without_extension).'.'.$extension;

                    $user->profile_image = $new_profile_image_filename;
                    Storage::move("public/profile/".$actual_profile_image_filename, "public/profile/" .$new_profile_image_filename);
                }
                if($user->banner_image !== self::DEFAULT_BANNER){
                    $actual_banner_image_filename = $user->banner_image;
                    $extension = pathinfo($actual_banner_image_filename, PATHINFO_EXTENSION);
                    $actual_profile_image_filename_without_extension = pathinfo($actual_banner_image_filename, PATHINFO_FILENAME);
                    $new_banner_image_filename = str_replace($last_username, $request->username, $actual_profile_image_filename_without_extension).'.'.$extension;
                    $user->banner_image = $new_banner_image_filename;
                    Storage::move("public/banner/" .$actual_banner_image_filename, "public/banner/" .$new_banner_image_filename);
                }

                $user->save();

                return response()->json(["success" => "Your username has been updated."]);
            } catch (\Throwable $error) {
                return response()->json(["error" => "A user with this username already exists."]);
            }
        }else{
            return response()->json([
                'error' => 'Invalid password.'
            ], 404);
        }
        

    }
}
