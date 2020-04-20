<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class UsersController extends Controller
{
  public function signup(Request $request) {
		$user = new User;

		$user->first_name = ucwords($request->first_name);
		$user->last_name = ucwords($request->last_name);
		$user->email = $request->email;
		$user->password = Hash::make($request->password);

		
		$data = [
			'names' => $user->first_name.' '.$user->last_name,
			'token' => Hash::make($request->last_name)
		];

		Mail::to($user->email)->send(new SendMail($data));

		$user->save();

		return response()->json([
			'status' => 201,
			'message' => 'User was created successfully',
			'data' => [
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'email' => $user->email,
			]
		], 201);
	}
}
