<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
	public function __construct()
    {
        //$this->middleware('auth');
    }

    public function showAll()
    {
		try {
			$rs = User::all();
			return $this->respondWithMessage(200, 'Done', $rs );
		} catch (\Exception $e) {
			return $this->respondWithMessage(404, 'Not Found', $rs );
		}
    }

    public function showOne($id)
    {
		try {
			$rs = User::findOrFail($id);
			return $this->respondWithMessage(200, 'Done', $rs );
		} catch (\Exception $e) {
			return $this->respondWithMessage(404, 'Not Found', $rs );
		}
    }
	
    public function create(Request $request)
    {
		$this->validate($request, [
            'name'     => 'required|string|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|confirmed:min:6',
		]);

		try {
			$password = $request->input('password');		
			
			$request->merge([
				'password' => app('hash')->make($password),
				'passwordf' => Crypt::encrypt($password)
			]);	

			$rs = User::create($request->all());
			return $this->respondWithMessage(201,'Created Successfully',$rs);

         } catch (\Exception $e) {
			return $this->respondWithMessage(409, 'Creation Failed');
         }

    }

    public function update($id, Request $request)
    {
		$this->validate($request, [
			'name'	  => 'string|unique:users,name,'.$id,  //'unique:table,column_to_check,id_to_ignore'
			'email'   => 'email|unique:users,email,'.$id,  
			'password' => 'confirmed|string|min:4',						
		]);
		
		try {
			$rs = User::findOrFail($id);
			

			$req = $request->all();
			
			if($request->has('password')) {			// se non la tocchi rimane la vecchia
				$password = $request->input('password');
				$req['password'] = app('hash')->make($password);
				$req['passwordf'] = Crypt::encrypt($password);
			}
			
			if($fotoNome = $this->uploadFoto($request)) 
				$req['foto'] = $fotoNome;
			
			
			
/* 			if($request->hasFile('foto')) {
				$img = $request->file('foto');
				$ext = $img->getClientOriginalExtension();
				$nam = Str::random(10) .'.'. $ext;
				$pwd = DIRECTORY_SEPARATOR .'app'. DIRECTORY_SEPARATOR .'public';
				
				$img->move(storage_path($pwd),$nam); 
				
				//return 'storage/app/public/'.$nam;
				$req['foto'] = $nam;
			} */

			$rs->update($req);
			return $this->respondWithMessage(200, 'Updated Successfully', $rs);
		
		} catch (\Exception $e) {
			return $this->respondWithMessage($e->getStatusCode() ?? 409, $e->getMessage() ?? 'Update failed');
		}
    }
	
	public function delete($id)
    {
		try {
			User::findOrFail($id)->delete();
			return $this->respondWithMessage(200, 'Deleted Successfully');
		} catch (\Exception $e) {
			return $this->respondWithMessage($e->getStatusCode() ?? 409, $e->getMessage() ?? 'Delete Failed');
		}
    }
	

}