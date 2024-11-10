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
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreignId('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreignId('attribute_id')->nullable();
            $table->foreign('attribute_id')->references('id')->on('product_attributes');

            $table->double('rate');
            $table->unsignedInteger('quantity');
            $table->double('total_amount');
            $table->string('discount_type')->nullable();
            $table->double('discount_amount')->nullable();
            $table->double('vat')->default(0);
            // $table->unsignedBigInteger('warranty_id')->nullable();
            // $table->foreign('warranty_id')->references('id')->on('warranties');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_details');
    }
};
