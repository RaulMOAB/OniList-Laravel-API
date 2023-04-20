<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;

class AccountController extends Controller
{
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
        //
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
    public function destroy(string $id)
    {
        $users = User::find($id);

        if ($users->delete()) {
            return response()->json([
                'status' => 'success',
                'message' => 'User was successfully deleted'
            ], 200);
        }

        // return response()->json([
        //     'status' => 'error',
        //     'message' => 'user not found'
        // ], 404);
    }
}
