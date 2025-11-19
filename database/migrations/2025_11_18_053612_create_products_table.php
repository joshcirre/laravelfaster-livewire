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
        Schema::create('products', function (Blueprint $table) {
            $table->string('slug')->primary();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('subcategory_slug');
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->foreign('subcategory_slug')->references('slug')->on('subcategories')->cascadeOnDelete();
            $table->index('subcategory_slug');

            // Only add fulltext index for MySQL/PostgreSQL
            if (in_array(config('database.default'), ['mysql', 'pgsql'])) {
                $table->fullText('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
