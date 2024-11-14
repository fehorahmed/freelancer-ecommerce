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
        Schema::create('shipping_charge_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_charge_limit_id');
            $table->foreign('shipping_charge_limit_id')->references('id')->on('shipping_charges');
            $table->foreignId('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->double('limit');
            $table->double('per_unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_charge_limits');
    }
};
