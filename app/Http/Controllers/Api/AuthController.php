<?php

namespace App\Http\Controllers\Api;
use App\User;
use App\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;

use JWTAuthException;
use DB;

class AuthController extends Controller
{
	private $user;
	private $jwtauth;

	public function __construct(User $user, JWTAuth $jwtauth)
	{
	$this->user = $user;
	$this->jwtauth = $jwtauth;
	}
	public function register(RegisterRequest $request)
	{
	$newUser = $this->user->create([
	  'name' => $request->get('name'),
	  'email' => $request->get('email'),
	  'UID' => $request->get('UID'),
	  'course' => $request->get('course'),
	  'password' => bcrypt($request->get('password'))
	]);
	if (!$newUser) {
	  return response()->json([‘failed_to_create_new_user’], 500);
	}
	//TODO: implement JWT
	//return response()->json([‘user_created’]);
	return response()->json([
    'token' => $this->jwtauth->fromUser($newUser)
  	]);
	}
	public function login(LoginRequest $request)
	{
	//TODO: authenticate JWT
		// get user credentials: email, password
	$creds_with_devicetoken = $request->only('email', 'password','devicetoken');
	$devicetoken = $request->only('devicetoken');
	$email = $request->only('email');
	error_log(json_encode($creds_with_devicetoken));
	$json_arrayofcreds = json_encode($creds_with_devicetoken);
	$json_decodedarray = json_decode($json_arrayofcreds);
	error_log($json_decodedarray->devicetoken);
	$d_token = $json_decodedarray->devicetoken;
	//app('App\Http\Controllers\FirebaseController')->inserttoken($devicetoken,$email);
	//return back()->withMessage('success');	//$query->save();

	$query = User::where('email','=',$email)->first();
	error_log($query->id);
	$id = $query->id;

	$user = User::find($id);
	$user->devicetoken = $d_token;
	$user->save();
	//$user->fill(['devicetoken' => $devicetoken]);

	$credentials = $request->only('email', 'password');
	$token = null;
	try {
	$token = $this->jwtauth->attempt($credentials);
	if (!$token) {
	return response()->json(['invalid_email_or_password'], 422);
	}
	} catch (JWTAuthException $e) {
	return response()->json(['failed_to_create_token'], 500);
	}
	return response()->json(compact('token'));
	}

	public function view($eid)
	{
		/*$user_details = User::find($id);
		$d = compact('user_details');
		error_log(json_encode($d));
		return response()->json(compact('user_details'));*/
		$user_details=DB::table('users')->select('name','email','course','UID')->where('email','=',$eid)->get();
		$d = compact('user_details');
		error_log(json_encode($d));
		return response()->json(compact('user_details'));
	}
}


