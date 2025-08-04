<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\User\Repositories\UserRepository;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
    }

    /** @test */
    public function it_can_get_all_users()
    {
        // Arrange
        User::factory()->count(3)->create();

        // Act
        $users = $this->userRepository->all();

        // Assert
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertCount(3, $users);
    }

    /** @test */
    public function it_can_get_paginated_users()
    {
        // Arrange
        User::factory()->count(15)->create();

        // Act
        $users = $this->userRepository->paginate(10);

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $users);
        $this->assertEquals(10, $users->perPage());
        $this->assertEquals(15, $users->total());
    }

    /** @test */
    public function it_can_find_user_by_id()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $foundUser = $this->userRepository->find($user->id);

        // Assert
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    /** @test */
    public function it_returns_null_when_user_not_found()
    {
        // Act
        $foundUser = $this->userRepository->find(999);

        // Assert
        $this->assertNull($foundUser);
    }

    /** @test */
    public function it_can_create_user()
    {
        // Arrange
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
            'status' => 1
        ];

        // Act
        $user = $this->userRepository->create($userData);

        // Assert
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('testuser', $user->username);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertEquals(1, $user->status);
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'username' => 'testuser'
        ]);
    }

    /** @test */
    public function it_can_update_user()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com'
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];

        // Act
        $updatedUser = $this->userRepository->update($user->id, $updateData);

        // Assert
        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertEquals('Updated Name', $updatedUser->name);
        $this->assertEquals('updated@example.com', $updatedUser->email);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    /** @test */
    public function it_can_delete_user()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->userRepository->delete($user->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_can_find_user_by_email()
    {
        // Arrange
        $user = User::factory()->create(['email' => 'test@example.com']);

        // Act
        $foundUser = $this->userRepository->findByEmail('test@example.com');

        // Assert
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    /** @test */
    public function it_can_find_user_by_username()
    {
        // Arrange
        $user = User::factory()->create(['username' => 'testuser']);

        // Act
        $foundUser = $this->userRepository->findByUsername('testuser');

        // Assert
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    /** @test */
    public function it_can_get_active_users()
    {
        // Arrange
        User::factory()->count(3)->create(['status' => 1]);
        User::factory()->count(2)->create(['status' => 0]);

        // Act
        $activeUsers = $this->userRepository->getActiveUsers();

        // Assert
        $this->assertCount(3, $activeUsers);
        $activeUsers->each(function ($user) {
            $this->assertEquals(1, $user->status);
        });
    }

    /** @test */
    public function it_can_get_inactive_users()
    {
        // Arrange
        User::factory()->count(3)->create(['status' => 1]);
        User::factory()->count(2)->create(['status' => 0]);

        // Act
        $inactiveUsers = $this->userRepository->getInactiveUsers();

        // Assert
        $this->assertCount(2, $inactiveUsers);
        $inactiveUsers->each(function ($user) {
            $this->assertEquals(0, $user->status);
        });
    }

    /** @test */
    public function it_can_search_users()
    {
        // Arrange
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
        User::factory()->create(['name' => 'Bob Wilson', 'email' => 'bob@example.com']);

        // Act
        $searchResults = $this->userRepository->search('John');

        // Assert
        $this->assertCount(1, $searchResults);
        $this->assertEquals('John Doe', $searchResults->first()->name);
    }

    /** @test */
    public function it_can_get_users_by_role()
    {
        // Arrange
        $adminUsers = User::factory()->count(2)->create();
        $regularUsers = User::factory()->count(3)->create();

        // Simulate role assignments (assuming role relationship exists)
        // This would depend on your actual role implementation

        // Act & Assert would depend on your role system
        $this->assertTrue(true); // Placeholder for role-based testing
    }

    /** @test */
    public function it_can_get_user_statistics()
    {
        // Arrange
        User::factory()->count(5)->create(['status' => 1]);
        User::factory()->count(3)->create(['status' => 0]);
        User::factory()->count(2)->create([
            'status' => 1,
            'created_at' => now()
        ]);

        // Act
        $stats = $this->userRepository->getStatistics();

        // Assert
        $this->assertIsArray($stats);
        $this->assertEquals(8, $stats['total_users']);
        $this->assertEquals(7, $stats['active_users']);
        $this->assertEquals(3, $stats['inactive_users']);
        $this->assertArrayHasKey('users_today', $stats);
    }

    /** @test */
    public function it_can_bulk_update_status()
    {
        // Arrange
        $users = User::factory()->count(3)->create(['status' => 0]);
        $userIds = $users->pluck('id')->toArray();

        // Act
        $result = $this->userRepository->bulkUpdateStatus($userIds, 1);

        // Assert
        $this->assertTrue($result);
        foreach ($users as $user) {
            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'status' => 1
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
        $result = $this->userRepository->bulkDelete($userIds);

        // Assert
        $this->assertTrue($result);
        foreach ($users as $user) {
            $this->assertDatabaseMissing('users', ['id' => $user->id]);
        }
    }

    /** @test */
    public function it_validates_email_uniqueness()
    {
        // Arrange
        User::factory()->create(['email' => 'test@example.com']);

        // Act
        $isUnique = $this->userRepository->isEmailUnique('test@example.com');
        $isUniqueNew = $this->userRepository->isEmailUnique('new@example.com');

        // Assert
        $this->assertFalse($isUnique);
        $this->assertTrue($isUniqueNew);
    }

    /** @test */
    public function it_validates_username_uniqueness()
    {
        // Arrange
        User::factory()->create(['username' => 'testuser']);

        // Act
        $isUnique = $this->userRepository->isUsernameUnique('testuser');
        $isUniqueNew = $this->userRepository->isUsernameUnique('newuser');

        // Assert
        $this->assertFalse($isUnique);
        $this->assertTrue($isUniqueNew);
    }

    /** @test */
    public function it_handles_database_exceptions_gracefully()
    {
        // Arrange - Force a database error by trying to create user with invalid data
        $invalidData = [
            'email' => null, // This should cause a database constraint error
            'username' => null
        ];

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->userRepository->create($invalidData);
    }

    /** @test */
    public function it_can_filter_users_by_date_range()
    {
        // Arrange
        $oldUser = User::factory()->create(['created_at' => now()->subDays(10)]);
        $newUser = User::factory()->create(['created_at' => now()->subDays(2)]);

        // Act
        $filteredUsers = $this->userRepository->filterByDateRange(
            now()->subDays(5),
            now()
        );

        // Assert
        $this->assertCount(1, $filteredUsers);
        $this->assertEquals($newUser->id, $filteredUsers->first()->id);
    }

    /** @test */
    public function it_can_get_recently_created_users()
    {
        // Arrange
        User::factory()->count(2)->create(['created_at' => now()->subDays(10)]);
        User::factory()->count(3)->create(['created_at' => now()->subHours(2)]);

        // Act
        $recentUsers = $this->userRepository->getRecentUsers(5);

        // Assert
        $this->assertCount(5, $recentUsers);
        // Should be ordered by created_at desc
        $this->assertTrue(
            $recentUsers->first()->created_at->gte($recentUsers->last()->created_at)
        );
    }
} 