<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('role',20);
            $table->string('user_name',100)->unique();
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('middle_name',100)->nullable();
            $table->string('suffix',5)->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->string('email', 100)->unique();
            $table->mediumInteger('company_id');
            $table->mediumInteger('branch_id');
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('status');
            $table->boolean('is_online');
            $table->string('profile_picture',100)->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
