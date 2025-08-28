<?php

namespace Tests\Feature;

use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestCase;
// use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPolicyTest extends TestCase
{
    // For testing, it is using test_database specified in phpunit.xml and .env.testing
    use RefreshDatabase; // automatically rolls back all changes after each test run
    // use DatabaseTransactions; // ✅ Keeps test data visible until transaction completes

    protected $admin;
    protected $employee;
    protected $anotherAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users for testing

        // Following relies on a custom state method defined in your UserFactory.php
        $this->admin = User::factory()->admin()->create(); // Explicitly set role as ADMIN

        // $this->employee = User::factory()->create(['role' => 'EMPLOYEE']);
        // (or) following relies on the factory state method employee()
        $this->employee = User::factory()->employee()->create(); // Explicitly set role as EMPLOYEE

        $this->anotherAdmin = User::factory()->admin()->create(); // Another ADMIN user
    }

    /** @test */
    public function admin_can_create_users()
    {
        $policy = new UserPolicy();
        $this->assertTrue($policy->create($this->admin)); // ✅ Allowed
    }

    /** @test */
    public function employee_cannot_create_any_users()
    {
        $policy = new UserPolicy();
        $this->assertFalse($policy->create($this->employee));
    }

    // -------------------------- End of testing Create User Policies ---------------------------

    /** @test */
    // Allow ADMIN users to update their own profile
    public function admin_can_update_themselves()
    {
        $policy = new UserPolicy();
        $this->assertTrue($policy->update($this->admin, $this->admin)); // ✅ Allowed
    }

    /** @test */
    // Allow EMPLOYEE users to update their own profile
    public function employee_can_update_themselves()
    {
        $policy = new UserPolicy();
        $this->assertTrue($policy->update($this->employee, $this->employee)); // ✅ Allowed
    }

    /** @test */
    // Ensure ADMIN can update EMPLOYEE users
    public function admin_can_update_employee_users()
    {
        $policy = new UserPolicy();
        $this->assertTrue($policy->update($this->admin, $this->employee)); // ✅ Allowed
    }

    /** @test */
    // Prevent ADMIN from updating other ADMINs
    public function admin_cannot_update_another_admin()
    {
        $policy = new UserPolicy();
        $this->assertFalse($policy->update($this->admin, $this->anotherAdmin)); // ❌ Denied
    }

    /** @test */
    // Prevent EMPLOYEE from updating other users
    public function employee_cannot_update_other_users()
    {
        $policy = new UserPolicy();
        $this->assertFalse($policy->update($this->employee, $this->admin)); // ❌ Denied
        $this->assertFalse($policy->update($this->employee, $this->anotherAdmin)); // ❌ Denied
    }

    // ------------------------------------- End of testing Update User Policies -------------------------------------

    /** @test */
    public function admin_can_delete_employee()
    {
        $policy = new UserPolicy();
        $this->assertTrue($policy->delete($this->admin, $this->employee));
    }

    /** @test */
    public function admin_cannot_delete_another_admin()
    {
        $policy = new UserPolicy();
        $this->assertFalse($policy->delete($this->admin, $this->anotherAdmin));
    }

    /** @test */
    public function admin_cannot_delete_themselves()
    {
        $policy = new UserPolicy();
        $this->assertFalse($policy->delete($this->admin, $this->admin));
    }

    /** @test */
    public function employee_cannot_delete_anyone()
    {
        $policy = new UserPolicy();
        $this->assertFalse($policy->delete($this->employee, $this->admin));
        $this->assertFalse($policy->delete($this->employee, $this->employee));
    }

    // ------------------------------------- End of testing Delete User Policies -------------------------------------
}
