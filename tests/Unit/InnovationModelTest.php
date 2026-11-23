<?php

namespace Tests\Unit;

use App\Enums\InnovationStatus;
use App\Models\Innovation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InnovationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_innovation_auto_generates_slug_and_casts_status_enum(): void
    {
        $user = User::factory()->create();

        $innovation = Innovation::create([
            'user_id' => $user->id,
            'title' => 'Digital Leadership Curriculum Portal',
            'description' => 'A comprehensive portal for public service curriculum transformation.',
            'status' => InnovationStatus::DRAFT,
        ]);

        $this->assertEquals('digital-leadership-curriculum-portal', $innovation->slug);
        $this->assertInstanceOf(InnovationStatus::class, $innovation->status);
        $this->assertEquals(InnovationStatus::DRAFT, $innovation->status);
        $this->assertTrue($innovation->status->isEditable());
        $this->assertTrue($innovation->isAuthor($user));
    }

    public function test_collaborator_relationship(): void
    {
        $author = User::factory()->create();
        $collaborator = User::factory()->create();

        $innovation = Innovation::create([
            'user_id' => $author->id,
            'title' => 'E-Governance Strategy 2027',
            'description' => 'Collaboration on government AI integration.',
            'status' => InnovationStatus::DRAFT,
        ]);

        $innovation->collaborators()->attach($collaborator->id, ['role_in_innovation' => 'Research Lead']);

        $this->assertTrue($innovation->isCollaborator($collaborator));
        $this->assertFalse($innovation->isCollaborator($author));
        $this->assertTrue($innovation->canBeEditedBy($collaborator));
    }
}
