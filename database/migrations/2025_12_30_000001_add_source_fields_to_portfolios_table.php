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
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('source_type')->nullable()->after('is_published'); // 'manual' or 'course'
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');

            $table->index(['user_id', 'source_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'source_type']);
            $table->dropColumn(['source_type', 'source_id']);
        });
    }
};
