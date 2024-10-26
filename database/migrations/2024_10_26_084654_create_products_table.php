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
            $table->id();
            $table->string('name');
            $table->string('url')->unique();
            $table->string('sku')->unique();
            $table->foreignId('brand_id')->nullable();
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreignId('unit_id')->nullable();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreignId('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreignId('warranty_id')->nullable();
            $table->foreign('warranty_id')->references('id')->on('warranties');
            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image');
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_apps_only')->default(0);
            $table->boolean('type')->default(0)->comment('1=comming soon, 2=pre-order, 0=reqular');

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
        Schema::dropIfExists('products');
    }
};
