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
        Schema::create('item_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_type'); // 'portfolio' or 'user_course'
            $table->unsignedBigInteger('item_id');
            $table->string('type'); // 'github', 'link', 'demo', 'pdf'
            $table->string('label')->nullable();
            $table->text('value'); // URL or path
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['user_id', 'item_type', 'item_id']);
            $table->index(['item_type', 'item_id']);
            $table->index(['item_type', 'item_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_evidences');
    }
};
