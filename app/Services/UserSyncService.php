<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserSyncService
{
    protected string $apiUrl;
    protected int $batchSize = 1000;
    protected int $maxBatchesPerHour = 50;

    public function __construct()
    {
        $this->apiUrl = env('SYNC_API_URL');
    }

    public function syncUsersInBatches(): void
    {
        $users = User::where('is_synced', false)->get();

        $batches = $users->chunk($this->batchSize);
        $batchCounter = 0;

        foreach ($batches as $batch) {
            if ($batchCounter >= $this->maxBatchesPerHour) {
                break;
            }

            $this->sendBatch($batch);
            $batchCounter++;
        }
    }

    protected function sendBatch($users): void
    {
        $subscribers = $users->map(function ($user) {
            return [
                'email' => $user->email,
                'time_zone' => $user->timezone,
                'name' => $user->firstname . ' ' . $user->lastname,
            ];
        })->toArray();

        $payload = [
            'batches' => [
                'subscribers' => $subscribers
            ]
        ];

        $response = Http::post($this->apiUrl, $payload);

        if ($response->successful()) {
            User::whereIn('email', $users->pluck('email'))->update(['is_synced' => true]);
        } else {
            Log::error('Failed to sync users', ['response' => $response->body()]);
        }
    }
}
