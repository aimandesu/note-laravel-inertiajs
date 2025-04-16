<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UpdateGuestActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guests:cleanup';
    protected $description = 'Delete inactive guest users and clear their sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inactiveGuests = User::where('isGuest', true)
            ->where('last_activity', '<', now()->subMinutes(config('session.lifetime')))
            ->get();

        foreach ($inactiveGuests as $guest) {
            // Delete related notes
            DB::table('notes')->where('user_id', $guest->id)->delete();

            // Delete sessions linked to this guest
            // DB::table('sessions')->where('user_id', $guest->id)->delete();

            // Delete the guest user
            $guest->delete();
        }

        $this->info('Inactive guest users cleaned up and sessions removed.');
    }

    // public function handle()
    // {
    //     // User::where('isGuest', true)
    //     // ->where('last_activity', '<', now()->subSeconds(30)) //after 2 minutes of any activity
    //     // ->delete();

    //     $inactiveGuests = User::where('isGuest', true)
    //     ->where('last_activity', '<', now()->subMinutes(config('session.lifetime')))
    //     ->get();

    //     foreach ($inactiveGuests as $guest) {
    //         // Delete related notes
    //         DB::table('notes')->where('user_id', session('guest_data.user_id'))->delete();
            
    //         // Delete sessions for this guest user
    //         DB::table('sessions')->where('id',session('guest_data.session_id'))->delete();
            
    //         // Delete the guest user
    //         $guest->delete();
    //     }

    // $this->info('Inactive guest users cleaned up and sessions removed.');
    // }
    
}
