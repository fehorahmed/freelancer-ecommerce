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
        Schema::create('campaign_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreignId('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreignId('product_attribute_id')->nullable();
            $table->foreign('product_attribute_id')->references('id')->on('product_attributes');
            $table->double('reguler_price')->nullable();
            $table->double('sell_price');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->foreignId('warranty_id')->nullable();
            $table->foreign('warranty_id')->references('id')->on('warranties');
            $table->boolean('status')->default(1);
            $table->foreignId('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('admins');
            $table->foreignId('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('admins');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_products');
    }
};
