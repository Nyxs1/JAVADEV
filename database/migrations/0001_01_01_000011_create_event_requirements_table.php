<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['info', 'checklist', 'tech'])->default('info');
            $table->string('category')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_requirements');
    }
};
