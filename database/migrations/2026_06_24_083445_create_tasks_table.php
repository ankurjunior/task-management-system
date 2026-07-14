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
        Schema::create('tasks', function (Blueprint $table) {

            $table->id();

            $table->string('task_name', 500);
            $table->longText('task_details')->nullable();

            $table->foreignId('priority_id')
                ->constrained('master_task_priorities');

            $table->foreignId('current_state_id')
                ->constrained('master_task_states');

            $table->foreignId('created_by_user_id')
                ->constrained('users');

            $table->foreignId('owner_user_id')
                ->constrained('users');

            // Normal task date
            $table->date('planned_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();

            // Perennial / ongoing task support
            $table->boolean('is_perennial')->default(false);
            $table->date('perennial_start_date')->nullable();
            $table->date('perennial_end_date')->nullable();

            $table->foreignId('update_frequency_id')
                ->nullable()
                ->constrained('master_update_frequencies');

            $table->date('last_update_date')->nullable();
            $table->date('next_update_due_date')->nullable();

            // Status flags
            $table->boolean('is_overdue')->default(false);
            $table->boolean('is_closed')->default(false);

            $table->timestamps();

            $table->index('owner_user_id');
            $table->index('created_by_user_id');
            $table->index('priority_id');
            $table->index('current_state_id');
            $table->index('update_frequency_id');
            $table->index('planned_completion_date');
            $table->index('next_update_due_date');
            $table->index('is_perennial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
