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
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('group_id')->nullable();
            $table->string('status'); // rejected, approved, returned
            $table->json('approve_sequence')->nullable(); // [{group_id: 1}, {user_id: 2}]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
