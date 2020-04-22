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
		
		$data = [
			'names' => $user->first_name.' '.$user->last_name,
			'token' => bcrypt($request->last_name)
		];

		Mail::to($user->email)->send(new SendMail($data));

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
}
