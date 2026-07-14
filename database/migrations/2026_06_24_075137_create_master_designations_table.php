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
        Schema::create('master_designations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('designation_type_id')
                ->constrained('master_designation_types');

            $table->string('name', 150)->unique();
            $table->string('short_name', 50)->nullable();

            $table->integer('hierarchy_level');

            $table->boolean('can_assign_task')->default(true);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('designation_id')
                ->references('id')
                ->on('master_designations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);
        });

        Schema::dropIfExists('master_designations');
    }
};
