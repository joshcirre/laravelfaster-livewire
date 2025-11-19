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
        Schema::create('subcategories', function (Blueprint $table) {
            $table->string('slug')->primary();
            $table->string('name');
            $table->foreignId('subcollection_id')->constrained()->cascadeOnDelete();
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->index('subcollection_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcategories');
    }
};
