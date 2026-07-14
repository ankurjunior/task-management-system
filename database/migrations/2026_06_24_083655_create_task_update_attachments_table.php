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
        Schema::create('task_update_attachments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('task_update_id')
                ->constrained('task_updates')
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by_user_id')
                ->constrained('users');

            $table->string('original_file_name');

            $table->string('stored_file_name');

            $table->string('file_path');

            $table->string('file_extension', 20)
                ->nullable();

            $table->unsignedBigInteger('file_size')
                ->nullable();

            $table->timestamps();

            $table->index('task_update_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_update_attachments');
    }
};
