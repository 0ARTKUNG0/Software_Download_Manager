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
        Schema::create('bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained()->onDelete('cascade');
            $table->foreignId('software_id')->constrained('software')->onDelete('cascade');
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->index('bundle_id');
            $table->unique(['bundle_id', 'software_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_items');
    }
};
