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
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('plane_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('starts_at_day')->nullable(false);
            $table->date('ends_at_day')->nullable(false);
            $table->time('starts_at_time')->nullable(false);
            $table->time('ends_at_time')->nullable(false);
            $table->timestamp('confirmed_at')->nullable(true);
            $table->unsignedBigInteger('confirmed_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['plane_id', 'starts_at_day']);
            $table->foreign('confirmed_by')->references('id')->on('users')->onUpdate('cascade')->restrictOnDelete();
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
