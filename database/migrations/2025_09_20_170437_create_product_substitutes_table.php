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
        Schema::create('product_substitutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('substitute_product_id')->constrained('products')->onDelete('cascade');
            $table->integer('priorytet')->default(0);
            $table->text('uwagi')->nullable();
            $table->timestamps();

            // Zapobieganie duplikatom i cyklom
            $table->unique(['product_id', 'substitute_product_id']);
            $table->index(['product_id', 'priorytet']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_substitutes');
    }
};
