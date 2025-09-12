<?php

namespace Tests\Unit\Policies;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use App\Models\Organisation;
use App\Policies\OrganisationPolicy;
use PHPUnit\Framework\MockObject\MockObject;

class OrganisationPolicyTest extends TestCase
{
    private OrganisationPolicy $policy;
    private MockObject $user;
    private MockObject $organisation;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->policy = new OrganisationPolicy();
        
        // Create mock objects
        $this->user = $this->createMock(User::class);
        $this->organisation = $this->createMock(Organisation::class);
    }

    /**
     * @test
     * @dataProvider adminAndEmployeeProvider
     */
    public function admin_can_create_organisations_but_employee_cannot(bool $isAdmin, bool $expected): void
    {
        // Arrange
        $this->user->method('isAdmin')->willReturn($isAdmin);

        // Act
        $result = $this->policy->create($this->user);

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider adminAndEmployeeProvider
     */
    public function admin_can_update_organisations_but_employee_cannot(bool $isAdmin, bool $expected): void
    {
        // Arrange
        $this->user->method('isAdmin')->willReturn($isAdmin);

        // Act
        $result = $this->policy->update($this->user, $this->organisation);

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider adminAndEmployeeProvider
     */
    public function admin_can_delete_organisations_but_employee_cannot(bool $isAdmin, bool $expected): void
    {
        // Arrange
        $this->user->method('isAdmin')->willReturn($isAdmin);

        // Act
        $result = $this->policy->delete($this->user, $this->organisation);

        // Assert
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for testing admin and employee permissions
     * 
     * @return array<string, array{bool, bool}> Array of test cases with [isAdmin, expectedResult]
     */
    public static function adminAndEmployeeProvider(): array
    {
        return [
            'when user is admin' => [true, true],
            'when user is employee' => [false, false],
        ];
    }
}