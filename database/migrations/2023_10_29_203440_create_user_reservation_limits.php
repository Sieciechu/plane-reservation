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
        Schema::create('user_reservation_limits', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable(false);
            $table->date('year_month')->nullable(false)->comment('Year and month');;
            $table->integer('granted_monthly_minutes')->nullable(false);
            $table->integer('used_monthly_minutes')->nullable(false)->default(0);
            $table->timestamps();

            $table->index(['user_id', 'year_month']);
            // $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reservation_limits');
    }
};
