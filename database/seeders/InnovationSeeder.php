<?php

namespace Database\Seeders;

use App\Enums\InnovationStatus;
use App\Models\Innovation;
use App\Models\InnovationStatusHistory;
use App\Models\ReviewerComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class InnovationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();

        if (! $innovator || ! $reviewer) {
            return;
        }

        // 1. Submitted Innovation
        $inn1 = Innovation::firstOrCreate(
            ['slug' => 'ai-powered-public-service-training-simulator'],
            [
                'user_id' => $innovator->id,
                'title' => 'AI-Powered Public Service Training Simulator',
                'description' => 'A simulated scenario training platform allowing public officers across Kenya to practice crisis management, ethics compliance, and decentralization policy execution.',
                'status' => InnovationStatus::SUBMITTED,
                'submitted_at' => now()->subDays(2),
            ]
        );

        InnovationStatusHistory::firstOrCreate(
            ['innovation_id' => $inn1->id, 'to_status' => InnovationStatus::SUBMITTED],
            [
                'changed_by_user_id' => $innovator->id,
                'from_status' => InnovationStatus::DRAFT,
                'remarks' => 'Initial submission for evaluation committee review.',
                'created_at' => now()->subDays(2),
            ]
        );

        // 2. Under Review Innovation
        $inn2 = Innovation::firstOrCreate(
            ['slug' => 'county-level-policy-impact-dashboard'],
            [
                'user_id' => $innovator->id,
                'title' => 'County-Level Policy Impact Dashboard',
                'description' => 'A centralized metrics dashboard to track how KSG executive leadership alumni implement devolved service delivery reforms across all 47 Kenyan counties.',
                'status' => InnovationStatus::UNDER_REVIEW,
                'submitted_at' => now()->subDays(5),
            ]
        );

        ReviewerComment::firstOrCreate(
            ['innovation_id' => $inn2->id, 'user_id' => $reviewer->id],
            [
                'comment' => 'Interesting proposal. Does the architecture leverage existing Council of Governors data endpoints or require new county integrations?',
                'created_at' => now()->subDays(1),
            ]
        );

        // 3. Draft Innovation
        Innovation::firstOrCreate(
            ['slug' => 'smart-campus-energy-optimization'],
            [
                'user_id' => $innovator->id,
                'title' => 'Smart Campus Energy Optimization at KSG Lower Kabete',
                'description' => 'IoT-based solar micro-grid management system designed to reduce campus power overhead by 35% during peak training symposiums.',
                'status' => InnovationStatus::DRAFT,
                'submitted_at' => null,
            ]
        );
    }
}
