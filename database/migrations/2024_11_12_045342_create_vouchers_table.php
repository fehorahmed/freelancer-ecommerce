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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('coupon_code');
            $table->integer('no_of_usage');
            $table->double('discount_amount')->nullable();
            $table->double('discount_percentage')->nullable();
            $table->date('start_date');
            $table->time('start_time')->nullable();
            $table->date('end_date');
            $table->enum('discountby',['amount','percentage']);
            $table->time('end_time')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->boolean('visibility')->default(1);
            $table->boolean('is_apps_only')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('admins');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
