<?php

namespace Tests\Unit\Policies;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Policies\UserPolicy;
use PHPUnit\Framework\MockObject\MockObject;

class UserPolicyTest extends TestCase
{
    private UserPolicy $policy;
    private MockObject $authUser;
    private MockObject $targetUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy();
    }

    /**
     * @test
     * @dataProvider adminAndEmployeeProvider
     */
    public function admin_can_create_users_but_employee_cannot(string $role, bool $expected): void
    {
        // Arrange
        $this->authUser = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->authUser->expects($this->any())
            ->method('__get')
            ->with('role')
            ->willReturn($role);

        // Act
        $result = $this->policy->create($this->authUser);

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider updatePermissionsProvider
     */
    public function admin_can_update_employees_and_users_can_update_themselves(
        string $authRole,
        string $targetRole,
        bool $isSameUser,
        bool $expected,
        string $scenario
    ): void {
        // Arrange
        $this->authUser = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->targetUser = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        // Set roles using __get mock
        $this->authUser->expects($this->any())
            ->method('__get')
            ->willReturnMap([
                ['role', $authRole],
                ['id', $isSameUser ? 1 : 1]
            ]);
            
        $this->targetUser->expects($this->any())
            ->method('__get')
            ->willReturnMap([
                ['role', $targetRole],
                ['id', $isSameUser ? 1 : 2]
            ]);

        // Act
        $result = $this->policy->update($this->authUser, $this->targetUser);

        // Assert
        $this->assertEquals($expected, $result, $scenario);
    }

    /**
     * @test
     * @dataProvider deletePermissionsProvider
     */
    public function admin_can_delete_employees_but_not_admins_or_self(
        string $authRole,
        string $targetRole,
        bool $isSameUser,
        bool $expected,
        string $scenario
    ): void {
        // Arrange
        $this->authUser = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->targetUser = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        // Set roles using __get mock
        $this->authUser->expects($this->any())
            ->method('__get')
            ->willReturnMap([
                ['role', $authRole],
                ['id', $isSameUser ? 1 : 1]
            ]);
            
        $this->targetUser->expects($this->any())
            ->method('__get')
            ->willReturnMap([
                ['role', $targetRole],
                ['id', $isSameUser ? 1 : 2]
            ]);

        // Act
        $result = $this->policy->delete($this->authUser, $this->targetUser);

        // Assert
        $this->assertEquals($expected, $result, $scenario);
    }

    /**
     * Data provider for testing basic admin/employee permissions
     */
    public static function adminAndEmployeeProvider(): array
    {
        return [
            'admin can create users' => ['ADMIN', true],
            'employee cannot create users' => ['EMPLOYEE', false],
        ];
    }

    /**
     * Data provider for testing update permissions
     */
    public static function updatePermissionsProvider(): array
    {
        return [
            'admin can update employee users' => [
                'ADMIN', 'EMPLOYEE', false, true,
                'Admin should be able to update employee users'
            ],
            'admin cannot update other admin users' => [
                'ADMIN', 'ADMIN', false, false,
                'Admin should not be able to update other admin users'
            ],
            'users can update their own profile' => [
                'EMPLOYEE', 'EMPLOYEE', true, true,
                'Users should be able to update their own profile'
            ],
            'employee cannot update other employees' => [
                'EMPLOYEE', 'EMPLOYEE', false, false,
                'Employee should not be able to update other employees'
            ],
            'employee cannot update admin users' => [
                'EMPLOYEE', 'ADMIN', false, false,
                'Employee should not be able to update admin users'
            ],
            'admin can update their own profile' => [
                'ADMIN', 'ADMIN', true, true,
                'Admin should be able to update their own profile'
            ],
        ];
    }

    /**
     * Data provider for testing delete permissions
     */
    public static function deletePermissionsProvider(): array
    {
        return [
            'admin can delete employee users' => [
                'ADMIN', 'EMPLOYEE', false, true,
                'Admin should be able to delete employee users'
            ],
            'admin cannot delete other admin users' => [
                'ADMIN', 'ADMIN', false, false,
                'Admin should not be able to delete other admin users'
            ],
            'admin cannot delete themselves' => [
                'ADMIN', 'ADMIN', true, false,
                'Admin should not be able to delete themselves'
            ],
            'employee cannot delete themselves' => [
                'EMPLOYEE', 'EMPLOYEE', true, false,
                'Employee should not be able to delete themselves'
            ],
            'employee cannot delete other employees' => [
                'EMPLOYEE', 'EMPLOYEE', false, false,
                'Employee should not be able to delete other employees'
            ],
            'employee cannot delete admin users' => [
                'EMPLOYEE', 'ADMIN', false, false,
                'Employee should not be able to delete admin users'
            ],
        ];
    }
}