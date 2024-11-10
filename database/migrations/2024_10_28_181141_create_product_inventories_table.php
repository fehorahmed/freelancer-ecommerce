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
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreignId('product_attribute_id')->nullable();
            $table->foreign('product_attribute_id')->references('id')->on('product_attributes');
            $table->double('stock_in')->nullable()->default(0);
            $table->double('stock_out')->nullable()->default(0);
            $table->integer('ref_type')->nullable();
            $table->string('reference')->nullable();
            $table->date('date');
            $table->boolean('status')->default(1);
            $table->foreignId('purchase_order_id')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('admins');
            $table->foreignId('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inventories');
    }
};
