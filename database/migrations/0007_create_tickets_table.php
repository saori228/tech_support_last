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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_number')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->text('description');
            $table->text('error_text');
            $table->dateTime('error_datetime');
            $table->dateTime('processing_deadline');
            $table->foreignId('status_id')->references('id')->on('ticket_statuses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

