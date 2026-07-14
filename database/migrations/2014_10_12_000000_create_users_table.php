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
            $table->string('username', 100)->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile', 20)->nullable();
            $table->string('employee_code', 50)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            $table->foreignId('role_id');

            $table->foreignId('designation_id')->nullable();

            $table->unsignedBigInteger('reporting_to_user_id')
                ->nullable();

            $table->foreign('reporting_to_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->timestamp('last_login_at')->nullable();

            $table->boolean('is_active')->default(true);

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
