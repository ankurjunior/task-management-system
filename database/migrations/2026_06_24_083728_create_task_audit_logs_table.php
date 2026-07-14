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
        Schema::create('task_audit_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->string('action', 100);

            $table->string('field_name', 100)
                ->nullable();

            $table->text('old_value')
                ->nullable();

            $table->text('new_value')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->timestamps();

            $table->index('task_id');
            $table->index('user_id');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_audit_logs');
    }
};
