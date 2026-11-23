<?php

namespace Tests\Feature;

use App\Enums\InnovationStatus;
use App\Models\Innovation;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InnovationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_guest_cannot_access_innovations(): void
    {
        $response = $this->get(route('innovations.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_innovator_can_view_innovations_dashboard(): void
    {
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();

        $response = $this->actingAs($innovator)->get(route('innovations.index'));
        $response->assertOk();
        $response->assertSee('Innovations Portal');
    }

    public function test_innovator_can_create_draft_innovation_with_attachments_and_collaborator(): void
    {
        Storage::fake('public');

        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();
        $collaborator = User::factory()->create(['name' => 'Co-Worker']);

        $file = UploadedFile::fake()->create('project_proposal.pdf', 500, 'application/pdf');

        $payload = [
            'title' => 'Automated KSG Exam Grading Engine',
            'description' => 'A machine-learning scoring system for public service leadership examinations.',
            'attachments' => [$file],
            'collaborators' => [
                ['user_id' => $collaborator->id, 'role' => 'Lead Data Scientist'],
            ],
        ];

        $response = $this->actingAs($innovator)->post(route('innovations.store'), $payload);

        $innovation = Innovation::where('title', 'Automated KSG Exam Grading Engine')->first();
        $this->assertNotNull($innovation);
        $this->assertEquals(InnovationStatus::DRAFT, $innovation->status);
        $this->assertEquals('automated-ksg-exam-grading-engine', $innovation->slug);

        $response->assertRedirect(route('innovations.show', $innovation));

        // Assert file stored
        $this->assertCount(1, $innovation->attachments);
        Storage::disk('public')->assertExists($innovation->attachments->first()->file_path);

        // Assert collaborator attached
        $this->assertTrue($innovation->isCollaborator($collaborator));

        // Assert status history recorded
        $this->assertDatabaseHas('innovation_status_histories', [
            'innovation_id' => $innovation->id,
            'to_status' => 'draft',
        ]);
    }

    public function test_validation_fails_if_title_or_description_too_short(): void
    {
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();

        $response = $this->actingAs($innovator)->post(route('innovations.store'), [
            'title' => 'No',
            'description' => 'Short',
        ]);

        $response->assertSessionHasErrors(['title', 'description']);
    }

    public function test_innovator_can_submit_draft_for_review(): void
    {
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();

        $innovation = Innovation::create([
            'user_id' => $innovator->id,
            'title' => 'Devolved Governance Training App',
            'description' => 'Mobile learning application for county government leadership personnel.',
            'status' => InnovationStatus::DRAFT,
        ]);

        $response = $this->actingAs($innovator)->post(route('innovations.submit', $innovation), [
            'remarks' => 'Ready for evaluation.',
        ]);

        $response->assertRedirect(route('innovations.show', $innovation));

        $innovation->refresh();
        $this->assertEquals(InnovationStatus::SUBMITTED, $innovation->status);
        $this->assertNotNull($innovation->submitted_at);

        $this->assertDatabaseHas('innovation_status_histories', [
            'innovation_id' => $innovation->id,
            'from_status' => 'draft',
            'to_status' => 'submitted',
        ]);
    }

    public function test_unauthorized_user_cannot_edit_others_innovation(): void
    {
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();
        $stranger = User::factory()->create();
        $stranger->assignRole('innovator');

        $innovation = Innovation::create([
            'user_id' => $innovator->id,
            'title' => 'KSG Cyber Defense Protocol',
            'description' => 'Confidential protocols for government network resilience.',
            'status' => InnovationStatus::DRAFT,
        ]);

        $response = $this->actingAs($stranger)->get(route('innovations.edit', $innovation));
        $response->assertForbidden();

        $responseUpdate = $this->actingAs($stranger)->put(route('innovations.update', $innovation), [
            'title' => 'Malicious Hijack Title',
            'description' => 'Attempting unauthorized update on another person innovation.',
        ]);
        $responseUpdate->assertForbidden();
    }
}
