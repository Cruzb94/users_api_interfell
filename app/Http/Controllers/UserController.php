<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserController extends Controller
{
     /**
     * Registro de usuario
     */
    public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'description' => 'required|string',
        ]);

        if($request->file('image')) {
            $imagename = $request->file('image')->getClientOriginalName();
            $path = $request->file('image')->storeAs('users', $imagename, 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description
        ]);


        if($user) {
            Profile::create([
                'user_id' => $user->id,
                'Ima_profile' => isset($path) ? $path : ""
            ]);   
        }

      
        return response()->json([
            'user' => $user->id,
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     *  Creación de token
     */
    public function login(Request $request)
    {
        $user = User::find(1);

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;

        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()
        ]);
    }

    /**
     * Cierre de sesión (anular el token)
     */
    public function logout(Request $request)
    {   
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function update(Request $request) {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'description' => 'required|string',
        ]);

        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->description = $request->description;
        $user->save();

        if($request->file('image')) {
            $imagename = $request->file('image')->getClientOriginalName();
            $path = $request->file('image')->storeAs('users', $imagename, 'public');
            $profile = Profile::where('user_id', $request->id)->first();
            $profile->Ima_profile = $path;
            $profile->save();
        }

        return response()->json([
            'message' => 'Successfully updated user'
        ]);

        
    }

    /**
     * Obtener el objeto User como json
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

     /**
     * Obtener el objeto Users como json
     */
    public function users()
    {
        return response()->json(User::with('image')->get());
    }

    /**
     * Borrar user
     */
    public function delete(Request $request) {

        $user = User::where('id', $request->id)->delete();

        if(!$user) {
            return response()->json([
                'message' => 'delete user error'], 400
            );
        } 

        return response()->json([
            'message' => 'Successfully deleted user'], 200
        );
    }

}