<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyAdminOfPopularUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-admin-of-popular-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify admin of users with more than 50 likes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $popularUsers = User::withCount('likesReceived')->having('likes_received_count', '>', 50)->get();

        foreach ($popularUsers as $user) {
            Log::info("User {$user->name} has more than 50 likes!");
            // Di sini Anda bisa menambahkan logika untuk mengirim email
        }

        $this->info('Checked for popular users.');
    }
}