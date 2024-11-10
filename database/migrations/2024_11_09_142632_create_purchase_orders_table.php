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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('supplier_id');
            $table->integer('max_index');
            $table->string('order_no');
            $table->double('sub_total_amount');
            $table->double('grand_total_amount');
            $table->double('shipping_charge')->default(0);
            $table->double('vat')->default(0);
            $table->double('discount')->default(0);
            $table->double('paid')->default(0);
            $table->double('due')->default(0);
            $table->foreignId('created_by')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('created_by')->references('id')->on('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
