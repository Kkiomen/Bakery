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
        Schema::create('recipe_step_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_step_id')->constrained('recipe_steps')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->decimal('ilosc', 10, 3);
            $table->string('jednostka');
            $table->text('uwagi')->nullable();
            $table->integer('kolejnosc')->nullable();
            $table->boolean('opcjonalny')->default(false);
            $table->string('sposob_przygotowania')->nullable();
            $table->integer('temperatura_c')->nullable();
            $table->timestamps();

            $table->unique(['recipe_step_id', 'material_id']);
            $table->index(['recipe_step_id', 'kolejnosc']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_step_materials');
    }
};
