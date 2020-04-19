<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;

class UserMiddleware {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|string|min:4',
			'last_name' => 'required|string|min:4',
			'email' => 'required|string|email|unique:users',
			'password' => 'required|string|min:8|confirmed'
	]);

	if ($validator->fails()) {
		return response()->json([
			'status' => 400,
			'message' => 'Bad Request',
			'data' => $validator->messages()
		], 400);
	}
		return $next($request);
	}
}
