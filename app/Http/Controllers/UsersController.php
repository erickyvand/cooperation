<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class UsersController extends Controller {
	
/**
 * @OA\Post(
 *   path="/api/signup",
 *   summary="Signup a user",
 * 	 @OA\Parameter(
 * 	   name="first_name",
 *     in="query",
 * 		 @OA\Schema(
 * 		   type="string"	
 * 		 )									
 * 	 ),
 * 	 @OA\Parameter(
 * 	   name="last_name",
 *     in="query",
 * 		 @OA\Schema(
 * 		   type="string"	
 * 		 )
 * 	 ),
 * 	 @OA\Parameter(
 * 	   name="email",
 *     in="query",
 * 		 @OA\Schema(
 * 		   type="string"	
 * 		 )
 * 	 ),
 * 	 @OA\Parameter(
 * 	   name="password",
 *     in="query",
 * 		 @OA\Schema(
 * 		   type="string"	
 * 		 )
 * 	 ),
 * 	 @OA\Parameter(
 * 	   name="password_confirmation",
 *     in="query",
 * 		 @OA\Schema(
 * 		   type="string"	
 * 		 )
 * 	 ),		
 *   @OA\Response(response=201, description="User was created successfully"),
 * 	 @OA\Response(response=400, description="Bad Request")	
 * )
 *
 * Create a user in a database.
 *
 * @return \Illuminate\Http\Response
 */
  public function signup(Request $request) {
		$user = new User;

		$user->first_name = ucwords($request->first_name);
		$user->last_name = ucwords($request->last_name);
		$user->email = $request->email;
		$user->password = bcrypt($request->password);

		$user->save();

		return response()->json([
			'status' => 201,
			'message' => 'User was created successfully',
			'data' => [
				'id' => $user->id,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'email' => $user->email,
			]
		], 201);
	}

	/**
	 * @OA\Post(
	 *   path="/api/login",
	 *   summary="Login a user",
	 * 	 @OA\Parameter(
	 *     name="email",
	 *     in="query",
	 *     @OA\Schema(
	 *       type="string"
	 *     )
	 *   ),
	 *   @OA\Parameter(
	 *     name="password",
	 *     in="query",
	 *     @OA\Schema(
	 *       type="String"
	 *     )
	 *   ),
	 *   @OA\Response(response=200, description="User logged in successfully"),	 	
	 *   @OA\Response(response=401, description="Unauthorized, invalid email or password, Please check your email to activate your account"),	 	
	 * )
	*/
	public function login(Request $request) {
		if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
			if ($request->user()->is_verified === 'true') {
				return response()->json([
					'status' => 200,
					'message' => 'User logged in successfully',
					'token' => auth()->user()->createToken('authorization')->accessToken,
					'data' => [
						'id' => $request->user()->id,
						'firstName' => $request->user()->first_name,
						'lastName' => $request->user()->last_name,
						'email' => $request->user()->email
					]
				], 200);
			} else {
				$data = ['names' => $request->user()->first_name.' '.$request->user()->last_name, 'token' => $request->user()->createToken('authorization')->accessToken];
		
				$request->session()->put('secret', $data['token']);
		
				Mail::to($request->user()->email)->send(new SendMail($data));
				return response()->json([
					'status' => 401,
					'error' => 'Please check your email to activate your account'
				], 401);
			}
		} else {
			return response()->json([
				'status' => 401,
				'error' => 'Unauthorized, invalid email or password'
			], 401);
		}
	}

	/**
	 * @OA\Patch(
	 * 	 path="/api/activate",
	 *   summary="Activate acount to be able to log in",
	 *   @OA\Parameter(
	 *     name="Authorization",
	 *     in="header",
	 *     @OA\Schema(
	 *       type="string"
	 *     )
	 *   ),
	 *   @OA\Response(response=200, description="Account successfully activated now you can login"),  
	 *   @OA\Response(response=401, description="Unauthorized, Invalid token")  
	 * )
	*/
	public function activateAccount(Request $request) {
		if ($request->session()->has('secret') && $request->header('authorization') !== $request->session()->get('secret')) {
			error_log($request->session()->get('secret'));
			return response()->json([
				'status' => 401,
				'error' => 'Unauthorized, Invalid token',
			], 401);
		} else {
			$user = User::find($request->user()->id);
			$user->is_verified = 'true';
			$user->save();
			return response()->json([
				'status' => 200,
				'message' => 'Account successfully activated now you can login'
			]);
		}
	}
}
