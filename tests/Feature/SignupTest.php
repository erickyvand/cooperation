<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignupTest extends TestCase {
	use RefreshDatabase;
	use WithFaker;
	
	/**
	 * A test method for creating a user.
	 *
	 * @return void
	 */
	public function testCreateUser() {
		$password = $this->faker->password;
		$user = [
			'first_name' => $this->faker->firstName,
			'last_name' => $this->faker->lastName,
			'email' => $this->faker->email,
			'password' => $password,
			'password_confirmation' => $password
		];
		$response = $this->json('POST', '/api/signup', $user);

		$response
			->assertStatus(201)
			->assertJsonStructure(['status', 'message', 'data'])
			->assertJsonFragment(['message' => 'User was created successfully']);
	}

	public function testUserInput() {
		$user = [
			'first_name' => $this->faker->firstName
		];
		$response = $this->json('POST', '/api/signup', $user);

		$response
			->assertStatus(400)
			->assertJsonStructure(['status', 'message', 'data'])
			->assertJsonFragment(['message' => 'Bad Request']);
	}
}
