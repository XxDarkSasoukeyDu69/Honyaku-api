<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function register(Request $request)
    {
            $validatedData = $request->validate([
                'pseudo'      => 'required|max:55',
                'email'     => 'email|required|unique:users',
                'password'  => 'required|confirmed',
            ]);

            if($validatedData) {

                $validatedData[ 'password' ] = bcrypt( $request->password );

                //  Mail::to($validatedData['email'])->send(new NewRegistration($validatedData));

                $user        =  User::create( $validatedData );
                $accessToken =  $user->createToken( 'authToken' )->accessToken;

                return response([
                    'user'          => $user,
                    'access_token'  => $accessToken,
                    'redirect'      => '/dashboard',
                    'status'        => 200
                ]);

            } else {
                abort(500);
            }

    }

    /**
     * details api
     *
     * @return JsonResponse
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json([
            'success' => $user
        ], $this-> successStatus);
    }


    public function login( Request $request )
    {

        $loginData = $request->validate([
            'email'     => 'email|required',
            'password'  => 'required'
        ]);

        if(!auth()->attempt( $loginData )) {
            return response([
                'message' => 'Invalid credentials'
            ], 422);
        }

        $accessToken = auth()->user()->createToken( 'authToken' )->accessToken;

        return response([
            'user'          => auth()->user(),
            'access_token'  => $accessToken
        ]);
    }
}
