<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tech_slug', 50);
            $table->string('tech_name', 100);
            $table->tinyInteger('level')->default(1); // 1=Novice, 2=Beginner, 3=Skilled, 4=Expert
            $table->timestamps();

            $table->unique(['user_id', 'tech_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_skills');
    }
};
