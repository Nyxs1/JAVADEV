<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');

            $table->enum('type', ['workshop', 'seminar', 'mentoring'])->default('workshop');
            $table->tinyInteger('level')->default(1)->comment('1=beginner, 2=intermediate, 3=advanced, 4=expert');
            $table->enum('mode', ['online', 'onsite', 'hybrid'])->default('online');

            $table->string('location_text')->nullable();
            $table->string('meeting_url')->nullable();
            $table->unsignedInteger('capacity')->nullable();

            $table->enum('status', ['draft', 'published', 'ended', 'cancelled'])->default('draft');
            $table->timestamp('finalized_at')->nullable();

            $table->json('requirements')->nullable();
            $table->string('cover_image')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }

};
