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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_number')->nullable();
            $table->integer('max_index');
            $table->string('order_no');
            $table->double('total_amount');
            $table->double('shipping_charge')->default(0);
            $table->double('vat')->default(0);
            $table->double('discount')->default(0);
            $table->string('coupon_code')->nullable();
            $table->foreignId('user_address_id')->nullable();
            $table->unsignedTinyInteger('order_create_type')->default(1)->comment('1=customer,2=admin');
            $table->foreignId('created_by')->nullable();

            $table->foreign('user_address_id')->references('id')->on('user_addresses');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
