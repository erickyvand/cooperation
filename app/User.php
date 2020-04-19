<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $table = 'users';
	protected $fillable = [
		'first_name',
		'last_name',
		'email',
		'password',
		'confirm_password'
	];
}
