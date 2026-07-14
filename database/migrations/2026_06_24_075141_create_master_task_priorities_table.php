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
        Schema::create('master_task_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('priority_name', 50)->unique();
            $table->string('priority_code', 20)->unique();
            $table->integer('sort_order');
            $table->integer('default_sla_days')->nullable();
            $table->string('color_code', 20)->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('master_task_priorities');
    }
};
