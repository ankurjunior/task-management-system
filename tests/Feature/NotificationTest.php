<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert([
            'id' => 1,
            'name' => 'Test role',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_user_can_open_their_notification_and_it_is_marked_as_read(): void
    {
        $user = User::factory()->create([
            'username' => 'notification-owner',
            'role_id' => 1,
        ]);
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'test',
            'title' => 'Test notification',
        ]);

        $response = $this->actingAs($user)->get(route('notifications.show', $notification));

        $response->assertRedirect(route('dashboard'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_open_another_users_notification(): void
    {
        $owner = User::factory()->create([
            'username' => 'notification-owner',
            'role_id' => 1,
        ]);
        $otherUser = User::factory()->create([
            'username' => 'notification-other',
            'role_id' => 1,
        ]);
        $notification = Notification::create([
            'user_id' => $owner->id,
            'type' => 'test',
            'title' => 'Private notification',
        ]);

        $this->actingAs($otherUser)
            ->get(route('notifications.show', $notification))
            ->assertForbidden();

        $this->assertNull($notification->fresh()->read_at);
    }
}
