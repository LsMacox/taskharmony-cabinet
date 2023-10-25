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
        Schema::create('user_workflow_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('workflow_id');
            $table->foreignId('group_id')->nullable();
            $table->boolean('is_approve');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_workflow_approvals');
    }
};
