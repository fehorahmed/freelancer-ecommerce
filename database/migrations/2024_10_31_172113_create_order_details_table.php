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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreignId('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreignId('attribute_id')->nullable();
            $table->foreign('attribute_id')->references('id')->on('product_attributes');
            $table->unsignedInteger('quantity');
            $table->string('discount_type');
            $table->double('discount_amount');
            $table->double('vat');
            $table->double('product_price');
            $table->double('net_price');
            $table->unsignedBigInteger('warranty_id')->nullable();
            $table->foreign('warranty_id')->references('id')->on('warranties');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
