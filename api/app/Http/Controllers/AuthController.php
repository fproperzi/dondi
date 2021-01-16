<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mails\MyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    
	public function __construct()
    {
       // $this->middleware('auth:api', ['except' => ['login']]);
    }
	public function login(Request $request)
    {		
		$this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required|string|min:4',
		]);

		if ($request->input( 'rememberme')) {
			config(['jwt.ttl' => env('JWT_TTL_REMEMBER_ME',  86400 * 30)]); // 30 days
		}
		
		$token = Auth::attempt([
			'email'    => $request->input( 'email' ),
			'password' => $request->input( 'password' ), 
			'active'   => 1
		]);
		
		if(! $token) return $this->respondWithMessage(401, 'Unauthorized' );
		else         return $this->respondWithToken($token);

    }
	
	public function recoverpwd(Request $request)   //data l'email ti spedisce la pwd
    {
		try {
			$user = User::where('email', $request->input('email'))->firstOrFail();
			
			$data = [
				'app_name' => env('APP_NAME'),
				'app_url'  => env('APP_URL'),
				'name'     => $user['name'],
				'email'    => $user['email'],
				'password' => Crypt::decrypt($user['passwordf'])
			];

			//Mail::to($email)->send(new MyEmail());
			
			Mail::send('email_recover_password', $data, function($message) use ($data) {
				$message
					->to($data['email'])
					->subject($data['app_name'].': Recupero della password');
			
			});
		
		} catch (\Exception $e) {
			return $this->respondWithMessage(401, 'User does not exist',$e );
		}
	}
	
	
	public function logout()
    {
		auth()->logout();
		return $this->respondWithMessage(401, 'Successfully logged out' );
    }
	
	public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

	public function profile()
    {
		return $this->respondWithMessage(200,'Done', Auth::user());
    }

}