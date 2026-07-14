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
        Schema::create('task_updates', function (Blueprint $table) {

            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->foreignId('updated_by_user_id')
                ->constrained('users');

            $table->foreignId('state_id')
                ->nullable()
                ->constrained('master_task_states');

            $table->decimal('completion_percentage', 5, 2)
                ->default(0);

            $table->date('expected_completion_date')
                ->nullable();

            $table->longText('remarks')
                ->nullable();

            $table->timestamps();

            $table->index('task_id');
            $table->index('updated_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_updates');
    }
};
