<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\User\Http\Requests\StoreUserRequest;
use Modules\User\Http\Requests\UpdateUserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

class FormRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function store_user_request_validates_required_fields()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        // Test required fields
        $validator = Validator::make([], $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** @test */
    public function store_user_request_validates_email_format()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** @test */
    public function store_user_request_validates_password_confirmation()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'different_password'
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** @test */
    public function store_user_request_validates_minimum_password_length()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => '123',
            'password_confirmation' => '123'
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** @test */
    public function store_user_request_passes_with_valid_data()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 1
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function update_user_request_does_not_require_password()
    {
        $request = new UpdateUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Updated User',
            'email' => 'updated@example.com'
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function update_user_request_validates_password_confirmation_when_provided()
    {
        $request = new UpdateUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'different_password'
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** @test */
    public function store_user_request_has_correct_authorization()
    {
        $request = new StoreUserRequest();
        
        // Test authorization (this depends on your permission system)
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function update_user_request_has_correct_authorization()
    {
        $request = new UpdateUserRequest();
        
        // Test authorization (this depends on your permission system)
        $this->assertTrue($request->authorize());
    }

    /** @test */
    public function store_user_request_has_custom_messages()
    {
        $request = new StoreUserRequest();
        $messages = $request->messages();
        
        $this->assertIsArray($messages);
        // You can test specific custom messages if they exist
    }

    /** @test */
    public function update_user_request_has_custom_messages()
    {
        $request = new UpdateUserRequest();
        $messages = $request->messages();
        
        $this->assertIsArray($messages);
        // You can test specific custom messages if they exist
    }

    /** @test */
    public function store_user_request_validates_username_minimum_length()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'ab', // Too short
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }

    /** @test */
    public function store_user_request_validates_username_maximum_length()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => str_repeat('a', 256), // Too long
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('username', $validator->errors()->toArray());
    }

    /** @test */
    public function store_user_request_validates_name_maximum_length()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => str_repeat('a', 256), // Too long
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** @test */
    public function store_user_request_validates_email_maximum_length()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Test User',
            'email' => str_repeat('a', 240) . '@example.com', // Too long
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** @test */
    public function store_user_request_validates_status_values()
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();

        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 999 // Invalid status
        ];

        $validator = Validator::make($data, $rules);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }
} 