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
        Schema::create(
            'workflow_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('workflow_id');
                $table->unsignedBigInteger('author_id');
                $table->string('status');
                $table->timestamps();

                $table
                    ->foreign('author_id')
                    ->references('id')
                    ->on('users');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_requests');
    }
};
