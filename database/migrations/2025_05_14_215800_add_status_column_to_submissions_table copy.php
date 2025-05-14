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
        Schema::table('submissions', function (Blueprint $table) {
            $table->enum('status', ['submitted', 'under_review', 'accepted', 'rejected', 'published', 'cancelled'])->default('submitted')->after('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
