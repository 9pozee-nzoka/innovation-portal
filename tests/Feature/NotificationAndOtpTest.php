<?php

namespace Tests\Feature;

use App\Enums\InnovationStatus;
use App\Models\Innovation;
use App\Models\User;
use App\Notifications\InnovationReviewedNotification;
use App\Notifications\InnovationSubmittedNotification;
use App\Notifications\SendOtpNotification;
use App\Services\OtpService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationAndOtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_submitting_innovation_dispatches_notification_to_reviewers(): void
    {
        Notification::fake();

        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();

        $innovation = Innovation::create([
            'user_id' => $innovator->id,
            'title' => 'Digital Curriculum Management System',
            'description' => 'Transforming academic management across all KSG campuses.',
            'status' => InnovationStatus::DRAFT,
        ]);

        $response = $this->actingAs($innovator)->post(route('innovations.submit', $innovation));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Assert notification sent to reviewer
        Notification::assertSentTo(
            $reviewer,
            InnovationSubmittedNotification::class,
            function (InnovationSubmittedNotification $notification) use ($innovation) {
                return $notification->innovation->id === $innovation->id;
            }
        );
    }

    public function test_reviewing_innovation_dispatches_notification_to_author_and_collaborators(): void
    {
        Notification::fake();

        $innovator = User::where('email', 'innovator@ksg.ac.ke')->first();
        $collaborator = User::factory()->create();
        $reviewer = User::where('email', 'reviewer@ksg.ac.ke')->first();

        $innovation = Innovation::create([
            'user_id' => $innovator->id,
            'title' => 'County Decentralized Healthcare Platform',
            'description' => 'Comprehensive medical record synchronizer for counties.',
            'status' => InnovationStatus::UNDER_REVIEW,
            'submitted_at' => now(),
        ]);
        $innovation->collaborators()->attach($collaborator->id, ['role_in_innovation' => 'Co-Author']);

        $this->actingAs($reviewer)->post(route('reviewer.review.store', $innovation), [
            'rating' => 5,
            'decision' => 'approved',
            'feedback' => 'Exceptional public health innovation ready for adoption.',
        ]);

        // Assert notification sent to both primary author and collaborator
        Notification::assertSentTo(
            $innovator,
            InnovationReviewedNotification::class,
            function (InnovationReviewedNotification $notification) {
                return $notification->review->rating === 5 &&
                       $notification->review->decision === 'approved' &&
                       str_contains($notification->review->feedback, 'ready for adoption');
            }
        );

        Notification::assertSentTo(
            $collaborator,
            InnovationReviewedNotification::class
        );
    }

    public function test_otp_generation_and_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $otpService = app(OtpService::class);

        $code = $otpService->generateAndSend($user);

        $this->assertEquals(6, strlen($code));
        $this->assertEquals($code, $user->fresh()->otp_code);
        $this->assertTrue($user->fresh()->otp_expires_at->isFuture());

        Notification::assertSentTo(
            $user,
            SendOtpNotification::class,
            fn (SendOtpNotification $notification) => $notification->otpCode === $code
        );
    }

    public function test_otp_verification_flow_succeeds_with_valid_code(): void
    {
        $user = User::factory()->create([
            'otp_code' => '654321',
            'otp_expires_at' => now()->addMinutes(10),
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('otp.verify.submit'), [
            'otp_code' => '654321',
        ]);

        $response->assertRedirect(route('innovations.index'));
        $this->assertNull($user->fresh()->otp_code);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_otp_verification_fails_with_invalid_code(): void
    {
        $user = User::factory()->create([
            'otp_code' => '654321',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post(route('otp.verify.submit'), [
            'otp_code' => '000000',
        ]);

        $response->assertSessionHasErrors(['otp_code']);
        $this->assertNotNull($user->fresh()->otp_code);
    }
}
