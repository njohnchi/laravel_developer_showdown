<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateUserDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command that updates user\'s firstname, lastname, and timezone to new random ones';

    /**
     * The available timezones.
     *
     * @var array
     */
    protected $timezones = ['CET', 'CST', 'GMT+1'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with Email {$email} not found.");
            return 1;
        }

        $firstname = fake()->name();
        $lastname = fake()->name();
        $timezone = $this->timezones[array_rand($this->timezones)];

        $user->update([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'timezone' => $timezone,
        ]);

        $this->info("Updated user {$user->email}: {$firstname} {$lastname}, Timezone: {$timezone}");

        return 0;
    }
}
