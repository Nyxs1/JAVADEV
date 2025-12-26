<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('registration_status', ['registered', 'cancelled', 'waitlist'])->default('registered');
            $table->enum('attendance_status', ['present', 'absent'])->nullable();
            $table->enum('completion_status', ['incomplete', 'completed'])->default('incomplete');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->string('certificate_url')->nullable();
            $table->text('reflection')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
