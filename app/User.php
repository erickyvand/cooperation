<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
	use HasApiTokens, Notifiable;

	protected $table = 'users';
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'password',
		'confirm_password'
	];
}
