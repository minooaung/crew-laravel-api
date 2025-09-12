<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Organisation;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use InvalidArgumentException;

/*
using Laravel's TestCase instead of PHPUnit's directly because:
It provides Laravel-specific testing features
It sets up the Laravel application for testing
It includes database testing capabilities
It provides additional Laravel-specific assertions
*/ 

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
    }

    /** @test */
    public function it_creates_employee_user_with_default_role()
    {
        // Arrange
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        // Act
        $user = $this->userService->create($userData);

        // Assert
        $this->assertEquals('EMPLOYEE', $user->role);
        $this->assertEquals('John Doe', $user->name);
        $this->assertNotEquals('password123', $user->password); // Password should be hashed
    }

    /** @test */
    public function it_creates_admin_user_when_limit_not_reached()
    {
        // Arrange
        // Create 4 admin users first
        User::factory()->count(4)->create(['role' => 'ADMIN']);
        
        $userData = [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'ADMIN'
        ];

        // Act
        $user = $this->userService->create($userData);

        // Assert
        $this->assertEquals('ADMIN', $user->role);
        $this->assertEquals(5, User::where('role', 'ADMIN')->count());
    }

    /** @test */
    public function it_throws_exception_when_admin_limit_reached()
    {
        // Arrange
        // Create 5 admin users first
        User::factory()->count(5)->create(['role' => 'ADMIN']);
        
        $userData = [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'ADMIN'
        ];

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot create ADMIN user. System already has maximum limit of 5 Admin Users.');
        
        $this->userService->create($userData);
    }

    /** @test */
    public function it_updates_user_without_changing_password()
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
        $updatedUser = $this->userService->update($user, $updateData);

        // Assert
        $this->assertEquals('Updated Name', $updatedUser->name);
        $this->assertEquals('updated@example.com', $updatedUser->email);
    }

    /** @test */
    public function it_updates_user_with_new_password()
    {
        // Arrange
        $user = User::factory()->create([
            'name' => 'Original Name',
            'password' => bcrypt('original_password')
        ]);
        
        $originalPassword = $user->password;
        
        $updateData = [
            'name' => 'Updated Name',
            'password' => 'new_password123'
        ];

        // Act
        $updatedUser = $this->userService->update($user, $updateData);

        // Assert
        $this->assertEquals('Updated Name', $updatedUser->name);
        $this->assertNotEquals($originalPassword, $updatedUser->password);
    }

    /** @test */
    public function it_throws_exception_when_updating_role_to_admin_and_limit_reached()
    {
        // Arrange
        User::factory()->count(5)->create(['role' => 'ADMIN']);
        
        $user = User::factory()->create(['role' => 'EMPLOYEE']);
        
        $updateData = [
            'role' => 'ADMIN'
        ];

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot change role to ADMIN. System already has maximum limit of 5 Admin Users.');
        
        $this->userService->update($user, $updateData);
    }

    /** @test */
    public function it_deletes_user_with_no_relationships()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->userService->delete($user);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_throws_exception_when_deleting_user_with_relationships()
    {
        // Arrange
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'ADMIN']);
        $organisation = Organisation::factory()->create();
        
        // Create the relationship in the pivot table
        $organisation->users()->attach($user->id, ['assigned_by' => $admin->id]);

        // Refresh the user model to ensure relationships are loaded
        $user = $user->fresh();

        // Assert & Act
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User cannot be deleted due to existing relationships.');
        
        $this->userService->delete($user);
    }
}