<?php

namespace App\Jobs;

use App\Mail\TaskAssigned;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTaskAssignedEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Maximum number of attempts.
     */
    public int $tries = 3;

    /**
     * Maximum execution time in seconds.
     */
    public int $timeout = 60;

    /**
     * Number of seconds between retries.
     */
    public array $backoff = [60, 300, 900];

    public function __construct(public Task $task)
    {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        $task = $this->task->load([
            'owner',
            'creator',
            'priority',
        ]);

        if (! $task->owner) {
            Log::warning('Task assigned email not sent: owner not found.', [
                'task_id' => $task->id,
            ]);

            return;
        }

        if (empty($task->owner->email)) {
            Log::warning('Task assigned email not sent: owner email missing.', [
                'task_id'  => $task->id,
                'owner_id' => $task->owner_user_id,
            ]);

            return;
        }

        Mail::to($task->owner->email)->send(new TaskAssigned($task));
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Task assigned email job failed.', [
            'task_id' => $this->task->id,
            'message' => $exception->getMessage(),
        ]);
    }
}
