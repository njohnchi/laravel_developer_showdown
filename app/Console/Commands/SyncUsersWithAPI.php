<?php

namespace App\Console\Commands;

use App\Services\UserSyncService;
use Illuminate\Console\Command;

class SyncUsersWithAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that syncs users with an external API';

    /**
     * Execute the console command.
     */
    public function handle(UserSyncService $userSyncService): int
    {
        try {
            $userSyncService->syncUsersInBatches();
        } catch (\Exception $e) {
            $this->error('Failed to sync users: ' . $e->getMessage());
            return 1;
        }

        $this->info('Users synced successfully!');
        return 0;
    }
}
