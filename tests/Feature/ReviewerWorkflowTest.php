<?php

namespace Tests\Feature;

use App\Enums\InnovationStatus;
use App\Models\Innovation;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_innovator_cannot_access_reviewer_dashboard(): void
    {
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();

        $response = $this->actingAs($innovator)->get(route('reviewer.dashboard'));
        $response->assertForbidden();
    }

    public function test_reviewer_can_access_reviewer_dashboard(): void
    {
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();

        $response = $this->actingAs($reviewer)->get(route('reviewer.dashboard'));
        $response->assertOk();
        $response->assertSee('Reviewer Dashboard');
    }

    public function test_opening_submitted_innovation_transitions_it_to_under_review(): void
    {
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();

        $innovation = Innovation::create([
            'user_id' => $innovator->id,
            'title' => 'Digital Archives for KSG Historic Lectures',
            'description' => 'Preserving 60 years of public administration symposium tapes.',
            'status' => InnovationStatus::SUBMITTED,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($reviewer)->get(route('reviewer.evaluate', $innovation));
        $response->assertOk();

        $innovation->refresh();
        $this->assertEquals(InnovationStatus::UNDER_REVIEW, $innovation->status);

        $this->assertDatabaseHas('innovation_status_histories', [
            'innovation_id' => $innovation->id,
            'from_status' => 'submitted',
            'to_status' => 'under_review',
        ]);
    }

    public function test_reviewer_can_post_inter_reviewer_comment(): void
    {
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();

        $innovation = Innovation::create([
            'user_id' => $innovator->id,
            'title' => 'County Procurement Fraud Detection Model',
            'description' => 'Graph database anomaly detection algorithms.',
            'status' => InnovationStatus::UNDER_REVIEW,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($reviewer)->post(route('reviewer.comment.store', $innovation), [
            'comment' => 'Has the legal compliance team reviewed the procurement data access terms?',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reviewer_comments', [
            'innovation_id' => $innovation->id,
            'user_id' => $reviewer->id,
            'comment' => 'Has the legal compliance team reviewed the procurement data access terms?',
        ]);
    }

    public function test_reviewer_can_submit_evaluation_decision_with_rating(): void
    {
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();

        $innovation = Innovation::create([
            'user_id' => $innovator->id,
            'title' => 'Civil Service Competency Matrix Automation',
            'description' => 'Standardized grading tool for promotions across all ministries.',
            'status' => InnovationStatus::UNDER_REVIEW,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($reviewer)->post(route('reviewer.review.store', $innovation), [
            'rating' => 4,
            'decision' => 'revision_requested',
            'feedback' => 'Strong framework, but please detail the integration with the Public Service Commission database.',
            'internal_notes' => 'Recommend approving once PSC schema is clarified.',
        ]);

        $response->assertRedirect(route('reviewer.dashboard'));

        $innovation->refresh();
        $this->assertEquals(InnovationStatus::REVISION_REQUESTED, $innovation->status);

        $this->assertDatabaseHas('reviews', [
            'innovation_id' => $innovation->id,
            'reviewer_id' => $reviewer->id,
            'rating' => 4,
            'decision' => 'revision_requested',
        ]);

        // Assert innovator sees feedback on show page
        $innovatorResponse = $this->actingAs($innovator)->get(route('innovations.show', $innovation));
        $innovatorResponse->assertOk();
        $innovatorResponse->assertSee('Strong framework, but please detail the integration');
        // Assert internal notes are NOT leaked to innovator
        $innovatorResponse->assertDontSee('Recommend approving once PSC schema is clarified.');
    }

    public function test_innovator_can_revise_and_resubmit_after_revision_requested(): void
    {
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();

        $innovation = Innovation::create([
            'user_id' => $innovator->id,
            'title' => 'Devolved Health Stock Tracker',
            'description' => 'Inventory system for public county medical facilities.',
            'status' => InnovationStatus::REVISION_REQUESTED,
            'submitted_at' => now()->subDays(2),
        ]);

        // Innovator can edit now because status is revision_requested
        $updateResponse = $this->actingAs($innovator)->put(route('innovations.update', $innovation), [
            'title' => 'Devolved Health Stock Tracker v2',
            'description' => 'Updated inventory system with complete offline sync protocols for remote health centers.',
        ]);
        $updateResponse->assertRedirect(route('innovations.show', $innovation));

        // Innovator resubmits
        $submitResponse = $this->actingAs($innovator)->post(route('innovations.submit', $innovation), [
            'remarks' => 'Incorporated offline sync as requested by reviewer.',
        ]);
        $submitResponse->assertRedirect(route('innovations.show', $innovation));

        $innovation->refresh();
        $this->assertEquals(InnovationStatus::RESUBMITTED, $innovation->status);

        // Reviewer can evaluate the resubmitted innovation and approve it
        $this->actingAs($reviewer)->post(route('reviewer.review.store', $innovation), [
            'rating' => 5,
            'decision' => 'approved',
            'feedback' => 'Excellent improvements! Approved for immediate KSG pilot deployment.',
        ]);

        $innovation->refresh();
        $this->assertEquals(InnovationStatus::APPROVED, $innovation->status);
    }

    public function test_reviewer_does_not_see_create_innovation_buttons(): void
    {
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();

        // Check innovations index view
        $response = $this->actingAs($reviewer)->get(route('innovations.index'));
        $response->assertOk();
        $response->assertDontSee('+ New Innovation');
        $response->assertDontSee('+ Create New Innovation');
        $response->assertDontSee('Submit Innovation');

        // Check reviewer dashboard view
        $dashboardResponse = $this->actingAs($reviewer)->get(route('reviewer.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertDontSee('+ New Innovation');
    }

    public function test_reviewer_cannot_access_create_innovation_page(): void
    {
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();

        $response = $this->actingAs($reviewer)->get(route('innovations.create'));
        $response->assertForbidden();
    }

    public function test_reviewer_dashboard_route_redirects_to_reviewer_workspace(): void
    {
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();

        $response = $this->actingAs($reviewer)->get(route('dashboard'));
        $response->assertRedirect(route('reviewer.dashboard'));
    }
}
