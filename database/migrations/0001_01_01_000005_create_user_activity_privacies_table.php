<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_activity_privacies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // 'portfolio', 'course', 'discussion', 'challenge'
            $table->boolean('is_public')->default(true); // true = public, false = private
            $table->timestamps();

            // Unique constraint - one privacy setting per user per activity type
            $table->unique(['user_id', 'activity_type']);

            // Index for performance
            $table->index(['user_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_privacies');
    }
};