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
        Schema::create('plane_reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('plane_id');
            $table->dateTime('starts_at')->nullable(false);
            $table->dateTime('ends_at')->nullable(false);
            $table->integer('time')->nullable(false)->comment('total minutes of reservation');
            $table->timestamp('confirmed_at')->nullable(true);
            $table->uuid('confirmed_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['plane_id', 'starts_at']);
            $table->index(['ends_at']);
            // $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->restrictOnDelete();
            // $table->foreign('plane_id')->references('id')->on('planes')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('confirmed_by')->references('id')->on('users')->onUpdate('cascade')->restrictOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plane_reservations');
    }
};
