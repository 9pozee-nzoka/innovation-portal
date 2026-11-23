<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('reviewer_status')->nullable()->after('phone')->index(); // 'pending', 'approved', 'rejected'
            $table->timestamp('reviewer_approved_at')->nullable()->after('reviewer_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reviewer_status', 'reviewer_approved_at']);
        });
    }
};
