<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
/*****
404	Not Found (page or other resource doesn’t exist)
401	Not authorized (not logged in)
403	Logged in but access to requested area is forbidden
400	Bad request (something wrong with URL or parameters)
422	Unprocessable Entity (validation failed)
500	General server error
******/
class Controller extends BaseController
{
	
    public function uploadFoto(Request $request)
    {	
		if($request->hasFile('foto') && $request->file('foto')->isValid()) {
			
			$img = $request->file('foto');

			$ext = $img->getClientOriginalExtension();
			$nam = Str::random(10) .'.'. $ext;
			$pwd = DIRECTORY_SEPARATOR .'app'. DIRECTORY_SEPARATOR .'public';
			
			if(! in_array($img->getMimeType(), ["image/jpeg", "image/gif", "image/png"])) abort(403,"file type not allowed"); 
			if(! $img->move(storage_path($pwd),$nam)) abort(401, "cant save file upload");
				
			return $nam;
		}
		return false;
    }
	


    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
	protected function respondWithMessage($status = 200,$message = 'Done', $data = null)
	{
		return response()->json([
			'message' => trans($message),
			'data'    => $data
		], $status);
	}
}