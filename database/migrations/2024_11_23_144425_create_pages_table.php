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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('url')->unique();
            $table->longText('text')->nullable();
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->foreignId('created_by');
            $table->foreign('created_by')->on('admins')->references('id');
            $table->foreignId('updated_by')->nullable();
            $table->foreign('updated_by')->on('admins')->references('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
