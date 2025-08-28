<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;
use App\Policies\OrganisationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganisationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $employee;
    protected $organisation;

    protected function setUp(): void
    {
        parent::setUp();

        // $this->admin = User::factory()->create(['role' => 'ADMIN']);
        $this->admin = User::factory()->admin()->create(); // Explicitly set role as ADMIN

        //$this->employee = User::factory()->create(['role' => 'EMPLOYEE']);
        $this->employee = User::factory()->employee()->create(); // Explicitly set role as EMPLOYEE

        $this->organisation = Organisation::factory()->create();

        // Attach organisation to employee for view() test
        $this->organisation->users()->attach($this->employee->id, ['assigned_by' => $this->admin->id]);
    }

    /** @test */
    public function admin_can_create_organisations()
    {
        $policy = new OrganisationPolicy();
        $this->assertTrue($policy->create($this->admin));
    }

    /** @test */
    public function employee_cannot_create_organisations()
    {
        $policy = new OrganisationPolicy();
        $this->assertFalse($policy->create($this->employee));
    }

    /** @test */
    public function admin_can_update_organisations()
    {
        $policy = new OrganisationPolicy();
        $this->assertTrue($policy->update($this->admin, $this->organisation));
    }

    /** @test */
    public function employee_cannot_update_organisations()
    {
        $policy = new OrganisationPolicy();
        $this->assertFalse($policy->update($this->employee, $this->organisation));
    }

    /** @test */
    public function admin_can_delete_organisations()
    {
        $policy = new OrganisationPolicy();
        $this->assertTrue($policy->delete($this->admin, $this->organisation));
    }

    /** @test */
    public function employee_cannot_delete_organisations()
    {
        $policy = new OrganisationPolicy();
        $this->assertFalse($policy->delete($this->employee, $this->organisation));
    }

    /** @test */
    // public function admin_can_view_any_organisation()
    // {
    //     $policy = new OrganisationPolicy();
    //     $this->assertTrue($policy->viewAny($this->admin));
    // }

    /** @test */
    // public function employee_can_view_any_organisation()
    // {
    //     $policy = new OrganisationPolicy();
    //     $this->assertTrue($policy->viewAny($this->employee));
    // }

    /** @test */
    // public function employee_can_view_assigned_organisation()
    // {
    //     $policy = new OrganisationPolicy();
    //     $this->assertTrue($policy->view($this->employee, $this->organisation));
    // }

    /** @test */
    // public function employee_cannot_view_unassigned_organisation()
    // {
    //     $unrelatedOrg = Organisation::factory()->create();

    //     $policy = new OrganisationPolicy();
    //     $this->assertFalse($policy->view($this->employee, $unrelatedOrg));
    // }
}
