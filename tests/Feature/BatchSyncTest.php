<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BatchSyncTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_users_are_synced_in_batches(): void
    {
        User::factory()->count(3000)->create(['is_synced' => false]);

        Http::fake();
        $this->artisan('users:sync')
            ->assertExitCode(0);

        Http::assertSentCount(3);
        $this->assertDatabaseMissing('users', ['is_synced' => false]);
    }
}
