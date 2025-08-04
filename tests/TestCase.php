<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed the database if needed
        // $this->seed();
        
        // Set up test environment
        config(['database.default' => 'testing']);
    }

    /**
     * Create a user factory if it doesn't exist
     */
    protected function createUser(array $attributes = [])
    {
        return \Modules\User\Entities\User::factory()->create($attributes);
    }

    /**
     * Create multiple users
     */
    protected function createUsers(int $count, array $attributes = [])
    {
        return \Modules\User\Entities\User::factory()->count($count)->create($attributes);
    }

    /**
     * Act as an authenticated user
     */
    protected function actingAsUser($user = null)
    {
        $user = $user ?: $this->createUser();
        return $this->actingAs($user);
    }
} 