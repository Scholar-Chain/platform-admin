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
        Schema::create('sync_error_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('external_id')->nullable();
            $table->string('assoc')->nullable();
            $table->json('payload');
            $table->text('error_message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_error_logs');
    }
};
