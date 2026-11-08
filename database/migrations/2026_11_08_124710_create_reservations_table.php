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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable()->unique();
            $table->string('nom');
            $table->string('business');
            $table->foreignId('mode_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('package_id')->constrained()->cascadeOnUpdate();
            $table->dateTime('creneau');
            $table->boolean('is_confirm')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
