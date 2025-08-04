<?php

namespace Tests\Unit;

use Tests\TestCase;
use Modules\User\Services\UserService;
use Modules\User\Repositories\UserRepository;
use Modules\User\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Mockery;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $userService;
    protected $userRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepositoryMock = Mockery::mock(UserRepository::class);
        $this->userService = new UserService($this->userRepositoryMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_all_users()
    {
        // Arrange
        $users = collect([
            new User(['name' => 'User 1']),
            new User(['name' => 'User 2'])
        ]);

        $this->userRepositoryMock
            ->shouldReceive('all')
            ->once()
            ->andReturn($users);

        // Act
        $result = $this->userService->getAllUsers();

        // Assert
        $this->assertEquals($users, $result);
    }

    /** @test */
    public function it_can_get_paginated_users()
    {
        // Arrange
        $paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
            collect([new User(['name' => 'User 1'])]),
            10,
            5
        );

        $this->userRepositoryMock
            ->shouldReceive('paginate')
            ->with(15)
            ->once()
            ->andReturn($paginatedUsers);

        // Act
        $result = $this->userService->getPaginatedUsers(15);

        // Assert
        $this->assertEquals($paginatedUsers, $result);
    }

    /** @test */
    public function it_can_create_user_with_valid_data()
    {
        // Arrange
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123',
            'status' => 1
        ];

        $expectedUser = new User($userData);

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->with(Mockery::on(function ($data) use ($userData) {
                return $data['name'] === $userData['name'] &&
                       $data['email'] === $userData['email'] &&
                       $data['username'] === $userData['username'] &&
                       Hash::check($userData['password'], $data['password']) &&
                       $data['status'] === $userData['status'];
            }))
            ->once()
            ->andReturn($expectedUser);

        // Act
        $result = $this->userService->createUser($userData);

        // Assert
        $this->assertEquals($expectedUser, $result);
    }

    /** @test */
    public function it_hashes_password_when_creating_user()
    {
        // Arrange
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'plaintext_password',
            'status' => 1
        ];

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->with(Mockery::on(function ($data) {
                return Hash::check('plaintext_password', $data['password']);
            }))
            ->once()
            ->andReturn(new User($userData));

        // Act
        $this->userService->createUser($userData);

        // Assert - Password should be hashed in the expectation above
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_update_user()
    {
        // Arrange
        $userId = 1;
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ];

        $updatedUser = new User(array_merge($updateData, ['id' => $userId]));

        $this->userRepositoryMock
            ->shouldReceive('update')
            ->with($userId, $updateData)
            ->once()
            ->andReturn($updatedUser);

        // Act
        $result = $this->userService->updateUser($userId, $updateData);

        // Assert
        $this->assertEquals($updatedUser, $result);
    }

    /** @test */
    public function it_hashes_password_when_updating_user_with_password()
    {
        // Arrange
        $userId = 1;
        $updateData = [
            'name' => 'Updated Name',
            'password' => 'new_password'
        ];

        $this->userRepositoryMock
            ->shouldReceive('update')
            ->with($userId, Mockery::on(function ($data) {
                return Hash::check('new_password', $data['password']);
            }))
            ->once()
            ->andReturn(new User(['id' => $userId]));

        // Act
        $this->userService->updateUser($userId, $updateData);

        // Assert - Password should be hashed in the expectation above
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_delete_user()
    {
        // Arrange
        $userId = 1;

        $this->userRepositoryMock
            ->shouldReceive('delete')
            ->with($userId)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->userService->deleteUser($userId);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_find_user_by_id()
    {
        // Arrange
        $userId = 1;
        $user = new User(['id' => $userId, 'name' => 'Test User']);

        $this->userRepositoryMock
            ->shouldReceive('find')
            ->with($userId)
            ->once()
            ->andReturn($user);

        // Act
        $result = $this->userService->findUser($userId);

        // Assert
        $this->assertEquals($user, $result);
    }

    /** @test */
    public function it_can_search_users()
    {
        // Arrange
        $searchTerm = 'John';
        $searchResults = collect([
            new User(['name' => 'John Doe']),
            new User(['name' => 'Johnny Smith'])
        ]);

        $this->userRepositoryMock
            ->shouldReceive('search')
            ->with($searchTerm)
            ->once()
            ->andReturn($searchResults);

        // Act
        $result = $this->userService->searchUsers($searchTerm);

        // Assert
        $this->assertEquals($searchResults, $result);
    }

    /** @test */
    public function it_can_get_user_statistics()
    {
        // Arrange
        $expectedStats = [
            'total_users' => 100,
            'active_users' => 80,
            'inactive_users' => 20,
            'users_today' => 5
        ];

        $this->userRepositoryMock
            ->shouldReceive('getStatistics')
            ->once()
            ->andReturn($expectedStats);

        // Act
        $result = $this->userService->getUserStatistics();

        // Assert
        $this->assertEquals($expectedStats, $result);
    }

    /** @test */
    public function it_can_activate_user()
    {
        // Arrange
        $userId = 1;
        $activatedUser = new User(['id' => $userId, 'status' => 1]);

        $this->userRepositoryMock
            ->shouldReceive('update')
            ->with($userId, ['status' => 1])
            ->once()
            ->andReturn($activatedUser);

        // Act
        $result = $this->userService->activateUser($userId);

        // Assert
        $this->assertEquals($activatedUser, $result);
    }

    /** @test */
    public function it_can_deactivate_user()
    {
        // Arrange
        $userId = 1;
        $deactivatedUser = new User(['id' => $userId, 'status' => 0]);

        $this->userRepositoryMock
            ->shouldReceive('update')
            ->with($userId, ['status' => 0])
            ->once()
            ->andReturn($deactivatedUser);

        // Act
        $result = $this->userService->deactivateUser($userId);

        // Assert
        $this->assertEquals($deactivatedUser, $result);
    }

    /** @test */
    public function it_can_bulk_activate_users()
    {
        // Arrange
        $userIds = [1, 2, 3];

        $this->userRepositoryMock
            ->shouldReceive('bulkUpdateStatus')
            ->with($userIds, 1)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->userService->bulkActivateUsers($userIds);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_bulk_deactivate_users()
    {
        // Arrange
        $userIds = [1, 2, 3];

        $this->userRepositoryMock
            ->shouldReceive('bulkUpdateStatus')
            ->with($userIds, 0)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->userService->bulkDeactivateUsers($userIds);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_can_bulk_delete_users()
    {
        // Arrange
        $userIds = [1, 2, 3];

        $this->userRepositoryMock
            ->shouldReceive('bulkDelete')
            ->with($userIds)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->userService->bulkDeleteUsers($userIds);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_validates_email_uniqueness()
    {
        // Arrange
        $email = 'test@example.com';

        $this->userRepositoryMock
            ->shouldReceive('isEmailUnique')
            ->with($email)
            ->once()
            ->andReturn(false);

        // Act
        $result = $this->userService->isEmailUnique($email);

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_validates_username_uniqueness()
    {
        // Arrange
        $username = 'testuser';

        $this->userRepositoryMock
            ->shouldReceive('isUsernameUnique')
            ->with($username)
            ->once()
            ->andReturn(false);

        // Act
        $result = $this->userService->isUsernameUnique($username);

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_validate_user_data()
    {
        // Arrange
        $validData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'password123'
        ];

        // Act
        $result = $this->userService->validateUserData($validData);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_throws_exception_for_invalid_user_data()
    {
        // Arrange
        $invalidData = [
            'name' => '', // Invalid: empty name
            'email' => 'invalid-email', // Invalid: malformed email
            'username' => 'ab', // Invalid: too short
            'password' => '123' // Invalid: too short
        ];

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->userService->validateUserData($invalidData);
    }

    /** @test */
    public function it_handles_repository_exceptions_gracefully()
    {
        // Arrange
        $this->userRepositoryMock
            ->shouldReceive('all')
            ->once()
            ->andThrow(new \Exception('Database connection failed'));

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database connection failed');
        $this->userService->getAllUsers();
    }

    /** @test */
    public function it_can_export_users_data()
    {
        // Arrange
        $users = collect([
            new User(['name' => 'User 1', 'email' => 'user1@example.com']),
            new User(['name' => 'User 2', 'email' => 'user2@example.com'])
        ]);

        $this->userRepositoryMock
            ->shouldReceive('all')
            ->once()
            ->andReturn($users);

        // Act
        $result = $this->userService->exportUsers();

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('email', $result[0]);
    }

    /** @test */
    public function it_can_get_users_by_status()
    {
        // Arrange
        $activeUsers = collect([
            new User(['name' => 'Active User 1', 'status' => 1]),
            new User(['name' => 'Active User 2', 'status' => 1])
        ]);

        $this->userRepositoryMock
            ->shouldReceive('getActiveUsers')
            ->once()
            ->andReturn($activeUsers);

        // Act
        $result = $this->userService->getActiveUsers();

        // Assert
        $this->assertEquals($activeUsers, $result);
    }
} 