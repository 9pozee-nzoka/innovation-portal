<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ReviewerAccessRequestedNotification;
use App\Notifications\ReviewerApprovedNotification;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReviewerApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_innovator_registers_and_gets_immediate_access(): void
    {
        $response = $this->post('/register', [
            'name' => 'Alice Innovator',
            'email' => 'alice@ksg.ac.ke',
            'department' => 'Lower Kabete - Public Policy',
            'role' => 'innovator',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'alice@ksg.ac.ke')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('innovator'));
        $this->assertNull($user->reviewer_status);
    }

    public function test_reviewer_registers_with_zero_portal_access_and_notifies_admin(): void
    {
        Notification::fake();

        $admin = User::where('email', 'admin@ksg.ac.ke')->first();

        $response = $this->post('/register', [
            'name' => 'Dr. Bob Candidate',
            'email' => 'bob@ksg.ac.ke',
            'department' => 'Mombasa Campus - Leadership',
            'phone' => '+254711223344',
            'role' => 'reviewer',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Zero access: User MUST NOT be authenticated
        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user = User::where('email', 'bob@ksg.ac.ke')->first();
        $this->assertNotNull($user);
        $this->assertEquals('pending', $user->reviewer_status);
        $this->assertFalse($user->hasRole('reviewer'));

        // Notification must have been sent to admin
        Notification::assertSentTo($admin, ReviewerAccessRequestedNotification::class, function ($notification) use ($user) {
            return $notification->applicant->id === $user->id;
        });
    }

    public function test_pending_reviewer_cannot_log_in(): void
    {
        $pendingUser = User::factory()->create([
            'email' => 'pending@ksg.ac.ke',
            'password' => bcrypt('password'),
            'reviewer_status' => 'pending',
        ]);

        $response = $this->post('/login', [
            'email' => 'pending@ksg.ac.ke',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_view_pending_reviewers(): void
    {
        $admin = User::where('email', 'admin@ksg.ac.ke')->first();

        User::factory()->create([
            'name' => 'Pending Reviewer 1',
            'email' => 'cand1@ksg.ac.ke',
            'reviewer_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reviewers.index'));

        $response->assertOk();
        $response->assertSee('Pending Reviewer 1');
        $response->assertSee('Approve Reviewer');
    }

    public function test_non_admin_cannot_access_reviewer_approval_portal(): void
    {
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();

        $response = $this->actingAs($innovator)->get(route('admin.reviewers.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_approve_reviewer_which_assigns_role_and_sends_notification(): void
    {
        Notification::fake();

        $admin = User::where('email', 'admin@ksg.ac.ke')->first();

        $applicant = User::factory()->create([
            'name' => 'Dr. Grace Evaluator',
            'email' => 'grace@ksg.ac.ke',
            'reviewer_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.reviewers.approve', $applicant));

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $applicant->refresh();
        $this->assertEquals('approved', $applicant->reviewer_status);
        $this->assertNotNull($applicant->reviewer_approved_at);
        $this->assertTrue($applicant->hasRole('reviewer'));

        // Notification must have been sent to applicant
        Notification::assertSentTo($applicant, ReviewerApprovedNotification::class);
    }

    public function test_approved_reviewer_can_now_log_in_and_access_reviewer_dashboard(): void
    {
        $approvedUser = User::factory()->create([
            'email' => 'approved@ksg.ac.ke',
            'password' => bcrypt('password'),
            'reviewer_status' => 'approved',
            'reviewer_approved_at' => now(),
        ]);
        $approvedUser->assignRole('reviewer');

        $loginResponse = $this->post('/login', [
            'email' => 'approved@ksg.ac.ke',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($approvedUser);
        $loginResponse->assertRedirect(route('dashboard', absolute: false));

        // Can access reviewer dashboard
        $dashboardResponse = $this->actingAs($approvedUser)->get(route('reviewer.dashboard'));
        $dashboardResponse->assertOk();
    }

    public function test_admin_cannot_access_create_innovation_page_and_does_not_see_submission_buttons(): void
    {
        $admin = User::where('email', 'admin@ksg.ac.ke')->first();

        // Admin cannot access create page
        $this->actingAs($admin)->get(route('innovations.create'))->assertForbidden();

        // Admin does not see New Innovation buttons
        $response = $this->actingAs($admin)->get(route('innovations.index'));
        $response->assertOk();
        $response->assertDontSee('+ New Innovation');
        $response->assertDontSee('Submit Innovation');

        // Admin does not see New Innovation in admin console
        $adminResponse = $this->actingAs($admin)->get(route('admin.reviewers.index'));
        $adminResponse->assertOk();
        $adminResponse->assertDontSee('+ New Innovation');
    }
}
