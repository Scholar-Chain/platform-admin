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
        Schema::disableForeignKeyConstraints();

        Schema::create('connector_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('token');
            $table->foreignId('author_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('publisher_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['author_id', 'publisher_id']);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connector_access_tokens');
    }
};
