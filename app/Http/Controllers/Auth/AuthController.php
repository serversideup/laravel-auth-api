<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Auth;

class AuthController extends Controller
{
    /**
     * Registers a user in our system
     * 
     * @param  Illuminate\Http\Request  $request
     */
    public function register( Request $request )
    {
        // Ensures that the data being submitted to register is valid
        $validator = Validator::make( $request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password'
        ]);
        
        // If everything is valid, create a new user, send an email verification email
        // and return a successful 204 response meaning an entity has been created.
        if( !$validator->fails() ){

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => bcrypt( $request->get('password') )
            ]);

            $user->sendApiEmailVerificationNotification();

            return response()->json('', 204 );
        }

        // Return the errors from the validator and a failure flag.
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    /**
     * Authenticates a user in the system
     * 
     * @param  Illuminate\Http\Request  $request
     */
    public function login( Request $request ){
        // If the request has 'device_name' it is coming from mobile, so we return a token
        if( $request->has('device_name') ){
            // Ensures the login request is valid
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required'
            ]);
            
            // Grab the user that matches the email and check to see if the
            // passwords match.
            $user = User::where('email', $request->email)->first();
            
            // If there is no user, or the password is incorrect, return a 403 error.
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => 'invalid_credentials'
                ], 403);
            }
            
            // Return the token for the user to the mobile app.
            return ['token' => $user->createToken($request->device_name)->plainTextToken ];
        }else{
            // Attempt to log in the user. If successful, update the gravatar and send a
            // 204 response code.
            if (Auth::attempt([
                'email' => $request->get('email'),
                'password' => $request->get('password')
            ])) {
                return response()->json('', 204 );
            }else{
                return response()->json([
                    'error' => 'invalid_credentials'
                ], 403);
            }
        }
    }


    /**
     * Logs out a user.
     * 
     * @param  Illuminate\Http\Request  $request
     */
    public function logout( Request $request ){
        Auth::logout();

        return response()->json('', 204);
    }
}