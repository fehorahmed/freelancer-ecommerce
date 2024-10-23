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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('root_id')->nullable();
            $table->foreign('root_id')->references('id')->on('categories');
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('horizontal_banner')->nullable();
            $table->string('vertical_banner')->nullable();
            $table->boolean('status')->default(1);
            $table->foreignId('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('admins');
            $table->foreignId('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('admins');
            $table->string('meta_title')->nullable();
            $table->mediumText('meta_description')->nullable();
            $table->mediumText('meta_keywords')->nullable();
            $table->mediumText('meta_og_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
