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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedTinyInteger('role')->default(1)->comment('1=User,2=Manager,3=Admin');


            $table->string('father_name')->nullable();
            $table->string('nid')->nullable();
            $table->string('nid_image')->nullable();
            $table->enum('job_type',['GOVT','PRIVATE'])->nullable();

            $table->foreignId('present_division_id')->nullable();
            $table->foreignId('present_district_id')->nullable();
            $table->foreignId('present_sub_district_id')->nullable();
            $table->string('present_address')->nullable();

            $table->foreignId('permanent_division_id')->nullable();
            $table->foreignId('permanent_district_id')->nullable();
            $table->foreignId('permanent_sub_district_id')->nullable();
            $table->string('permanent_address')->nullable();

            $table->foreignId('office_division_id')->nullable();
            $table->foreignId('office_district_id')->nullable();
            $table->foreignId('office_sub_district_id')->nullable();
            $table->string('office_address')->nullable();


            $table->boolean('status')->default(1);


            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
