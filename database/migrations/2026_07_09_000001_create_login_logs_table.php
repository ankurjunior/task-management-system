<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('username', 100)->nullable();
            $table->string('event_type', 30);
            $table->string('status', 30);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('failure_reason', 255)->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();

            $table->index('user_id');
            $table->index('username');
            $table->index('event_type');
            $table->index('status');
            $table->index('logged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
