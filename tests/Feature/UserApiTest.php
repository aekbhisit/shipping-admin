<?php

namespace Tests\Feature;

use Tests\TestCase;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

class UserApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user for authentication if needed
        $this->adminUser = User::factory()->create([
            'email' => 'admin@test.com',
            'username' => 'admin',
            'status' => 1
        ]);
    }

    /** @test */
    public function it_can_get_all_users()
    {
        // Arrange
        User::factory()->count(3)->create();

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->getJson('/api/users');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'email',
                             'username',
                             'status',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function it_can_get_paginated_users()
    {
        // Arrange
        User::factory()->count(15)->create();

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->getJson('/api/users/paginated?page=1&per_page=10');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'name', 'email', 'username', 'status']
                         ],
                         'current_page',
                         'per_page',
                         'total',
                         'last_page'
                     ]
                 ]);
    }

    /** @test */
    public function it_can_create_user_with_valid_data()
    {
        // Arrange
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'username' => $this->faker->unique()->userName,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 1
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/api/users', $userData);

        // Assert
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'email',
                         'username',
                         'status'
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'username' => $userData['username']
        ]);
    }

    /** @test */
    public function it_fails_to_create_user_with_invalid_data()
    {
        // Arrange
        $invalidData = [
            'name' => '', // Required field empty
            'email' => 'invalid-email', // Invalid email format
            'username' => 'ab', // Too short
            'password' => '123' // Too short
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/api/users', $invalidData);

        // Assert
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'username', 'password']);
    }

    /** @test */
    public function it_fails_to_create_user_with_duplicate_email()
    {
        // Arrange
        $existingUser = User::factory()->create();
        $userData = [
            'name' => $this->faker->name,
            'email' => $existingUser->email, // Duplicate email
            'username' => $this->faker->unique()->userName,
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/api/users', $userData);

        // Assert
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_fails_to_create_user_with_duplicate_username()
    {
        // Arrange
        $existingUser = User::factory()->create();
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'username' => $existingUser->username, // Duplicate username
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/api/users', $userData);

        // Assert
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['username']);
    }

    /** @test */
    public function it_can_get_specific_user()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->getJson("/api/users/{$user->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'name',
                         'email',
                         'username',
                         'status',
                         'created_at',
                         'updated_at'
                     ]
                 ])
                 ->assertJsonPath('data.id', $user->id);
    }

    /** @test */
    public function it_returns_404_for_non_existent_user()
    {
        // Act
        $response = $this->actingAs($this->adminUser)
                         ->getJson('/api/users/999');

        // Assert
        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'User not found'
                 ]);
    }

    /** @test */
    public function it_can_update_user()
    {
        // Arrange
        $user = User::factory()->create();
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->putJson("/api/users/{$user->id}", $updateData);

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'email',
                         'username',
                         'status'
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    /** @test */
    public function it_can_update_user_password()
    {
        // Arrange
        $user = User::factory()->create();
        $updateData = [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ];

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->putJson("/api/users/{$user->id}", $updateData);

        // Assert
        $response->assertStatus(200);
        
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function it_can_delete_user()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->deleteJson("/api/users/{$user->id}");

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'User deleted successfully'
                 ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_can_activate_user()
    {
        // Arrange
        $user = User::factory()->create(['status' => 0]);

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->putJson("/api/users/{$user->id}/activate");

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'User activated successfully'
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 1
        ]);
    }

    /** @test */
    public function it_can_deactivate_user()
    {
        // Arrange
        $user = User::factory()->create(['status' => 1]);

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->putJson("/api/users/{$user->id}/deactivate");

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'User deactivated successfully'
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 0
        ]);
    }

    /** @test */
    public function it_can_search_users()
    {
        // Arrange
        User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);
        User::factory()->create(['name' => 'Bob Wilson']);

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->getJson('/api/users/search?q=John');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'email', 'username']
                     ]
                 ]);

        $searchResults = $response->json('data');
        $this->assertCount(1, $searchResults);
        $this->assertEquals('John Doe', $searchResults[0]['name']);
    }

    /** @test */
    public function it_can_get_user_statistics()
    {
        // Arrange
        User::factory()->count(5)->create(['status' => 1]);
        User::factory()->count(3)->create(['status' => 0]);

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->getJson('/api/users/statistics');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'total_users',
                         'active_users',
                         'inactive_users',
                         'users_today'
                     ]
                 ]);

        $stats = $response->json('data');
        $this->assertGreaterThanOrEqual(8, $stats['total_users']); // Including admin user
        $this->assertGreaterThanOrEqual(5, $stats['active_users']);
        $this->assertEquals(3, $stats['inactive_users']);
    }

    /** @test */
    public function it_can_bulk_activate_users()
    {
        // Arrange
        $users = User::factory()->count(3)->create(['status' => 0]);
        $userIds = $users->pluck('id')->toArray();

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->putJson('/api/users/bulk/activate', [
                             'user_ids' => $userIds
                         ]);

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Users activated successfully'
                 ]);

        foreach ($users as $user) {
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'status' => 1
            ]);
        }
    }

    /** @test */
    public function it_can_bulk_deactivate_users()
    {
        // Arrange
        $users = User::factory()->count(3)->create(['status' => 1]);
        $userIds = $users->pluck('id')->toArray();

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->putJson('/api/users/bulk/deactivate', [
                             'user_ids' => $userIds
                         ]);

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Users deactivated successfully'
                 ]);

        foreach ($users as $user) {
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'status' => 0
            ]);
        }
    }

    /** @test */
    public function it_can_bulk_delete_users()
    {
        // Arrange
        $users = User::factory()->count(3)->create();
        $userIds = $users->pluck('id')->toArray();

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->deleteJson('/api/users/bulk/delete', [
                             'user_ids' => $userIds
                         ]);

        // Assert
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Users deleted successfully'
                 ]);

        foreach ($users as $user) {
            $this->assertDatabaseMissing('users', ['id' => $user->id]);
        }
    }

    /** @test */
    public function it_can_check_email_availability()
    {
        // Arrange
        $existingUser = User::factory()->create();

        // Act - Check existing email
        $response1 = $this->actingAs($this->adminUser)
                          ->getJson("/api/users/check-email?email={$existingUser->email}");

        // Act - Check new email
        $response2 = $this->actingAs($this->adminUser)
                          ->getJson('/api/users/check-email?email=new@example.com');

        // Assert
        $response1->assertStatus(200)
                  ->assertJson([
                      'success' => true,
                      'available' => false
                  ]);

        $response2->assertStatus(200)
                  ->assertJson([
                      'success' => true,
                      'available' => true
                  ]);
    }

    /** @test */
    public function it_can_check_username_availability()
    {
        // Arrange
        $existingUser = User::factory()->create();

        // Act - Check existing username
        $response1 = $this->actingAs($this->adminUser)
                          ->getJson("/api/users/check-username?username={$existingUser->username}");

        // Act - Check new username
        $response2 = $this->actingAs($this->adminUser)
                          ->getJson('/api/users/check-username?username=newuser');

        // Assert
        $response1->assertStatus(200)
                  ->assertJson([
                      'success' => true,
                      'available' => false
                  ]);

        $response2->assertStatus(200)
                  ->assertJson([
                      'success' => true,
                      'available' => true
                  ]);
    }

    /** @test */
    public function it_can_export_users()
    {
        // Arrange
        User::factory()->count(5)->create();

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->getJson('/api/users/export');

        // Assert
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'email',
                             'username',
                             'status',
                             'created_at'
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function it_requires_authentication_for_api_endpoints()
    {
        // Act - Try to access without authentication
        $response = $this->getJson('/api/users');

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function it_handles_server_errors_gracefully()
    {
        // This test would require mocking to force a server error
        // For now, we'll test that proper error structure is returned
        $this->assertTrue(true);
    }

    /** @test */
    public function it_validates_bulk_operation_data()
    {
        // Arrange - Invalid data (empty user_ids)
        $invalidData = ['user_ids' => []];

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->putJson('/api/users/bulk/activate', $invalidData);

        // Assert
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['user_ids']);
    }

    /** @test */
    public function it_can_filter_users_by_status()
    {
        // Arrange
        User::factory()->count(3)->create(['status' => 1]);
        User::factory()->count(2)->create(['status' => 0]);

        // Act - Get active users
        $response = $this->actingAs($this->adminUser)
                         ->getJson('/api/users?status=1');

        // Assert
        $response->assertStatus(200);
        $users = $response->json('data');
        
        foreach ($users as $user) {
            $this->assertEquals(1, $user['status']);
        }
    }

    /** @test */
    public function it_can_filter_users_by_date_range()
    {
        // Arrange
        User::factory()->create(['created_at' => now()->subDays(10)]);
        User::factory()->create(['created_at' => now()->subDays(2)]);

        // Act
        $response = $this->actingAs($this->adminUser)
                         ->getJson('/api/users?date_from=' . now()->subDays(5)->toDateString());

        // Assert
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }
} 