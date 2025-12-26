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
        Schema::create('role_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('to_role_id')->constrained('roles')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reason')->nullable(); // Alasan request
            $table->text('admin_notes')->nullable(); // Catatan admin saat approve/reject
            $table->timestamp('processed_at')->nullable(); // Kapan diproses
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang memproses
            $table->timestamps();

            // Index untuk performa
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_requests');
    }
};