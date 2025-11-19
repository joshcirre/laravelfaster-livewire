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
        Schema::create('subcollections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category_slug');
            $table->timestamps();

            $table->foreign('category_slug')->references('slug')->on('categories')->cascadeOnDelete();
            $table->index('category_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcollections');
    }
};
