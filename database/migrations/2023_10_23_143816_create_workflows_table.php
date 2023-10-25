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
            'workflows', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('group_id')->nullable();
                $table->text('state'); // approved, rejected, returned
                $table->tinyInteger('status'); // moderation
                $table->dateTime('moderated_at')->nullable();
                $table->unsignedBigInteger('moderated_by')->nullable();
                $table->json('approve_sequence')->nullable(); // [{group_id: 1}, {user_id: 2}]
                $table->timestamps();

                $table->foreign('moderated_by')->on('users')->references('id');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
