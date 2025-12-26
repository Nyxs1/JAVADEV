<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_requirement_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requirement_id')->constrained('event_requirements')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_checked')->default(false);
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'requirement_id', 'user_id'], 'event_req_check_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_requirement_checks');
    }
};
