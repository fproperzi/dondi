<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:api', [ 'except' => ['login','register','allAccess'] ]);
  }

  /**
   * Store a new user.
   *
   * @param  Request  $request
   * @return Response
   */
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'username'  => 'required|unique:users|between:3,50',
      'email'     => 'required|email|unique:users|max:50',
      'password'  => 'required|string||between:6,40',
    ]);

    if ($validator->fails()) {  
      return response()->json($validator->errors(), 400);  
    }  

    $user = User::create(
              array_merge(
                $validator->validated(),
                ['password' => app('hash')->make($request->password)]
              )
            );

    return response()->json([
      'message' => 'User was registered successfully!'
    ], 201);
  }
  
   /**
   * Get a JWT via given credentials.
   *
   * @param  Request  $request
   * @return Response
   */  
  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'username' => 'required',
      'password' => 'required|string|min:6',
    ]);
    if ($validator->fails()) {  
      return response()->json(
        [
          'accessToken' => null,
          'message'     => 'Login failed: Invalid username or password.'
        ], 400);
    }  
    if (! $token = auth()->attempt($validator->validated())) {
      return response()->json(
        [
          'accessToken' => null,
          'message'     => 'No token provided!. Please try again.'
        ], 400);
    }
    return response()->json(
      [  
        'id' => auth()->user()->id,
        'username' => auth()->user()->username,
        'email' => auth()->user()->email,
        'created_at' => auth()->user()->created_at,
        'accessToken' => $token,  
        'token_type' => 'bearer',  
        'expires_in' => Auth::factory()->getTTL() * 60  
      ], 200);  
  }

  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    auth()->logout();
    return response()->json(['message' => 'Successfully logged out']);
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    return $this->createNewToken(auth()->refresh());
  }

  public function allAccess()
  { 
    return 'Authentication with JSON Web Token (JWT)';
  }

}
