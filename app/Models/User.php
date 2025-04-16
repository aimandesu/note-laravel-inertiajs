<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;


class User extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'isGuest',
        'last_activity',
    ];

    public function user()
    {
        return $this->hasMany(Note::class);
    }

    public static function getGuestId()
    {
        if (!session()->has('guest_data.user_id')) {

            //if based on users activity  -> doesnt work bcs well it still in the end session only 1 minute
            // $expired = DB::table('users')
            // ->where('isGuest', true)
            // ->where('last_activity', '<', now()->subMinutes(config('session.lifetime')))
            // ->count();

            //if based on sessions
            // $expired = DB::table('sessions')
            // ->where('last_activity', '<', now()->subMinutes(config('session.lifetime'))->timestamp)
            // ->count();
            
            // if ($expired) {
            //     Artisan::call('guests:cleanup');
            // }

            $guest = self::create([
                'name' => 'Guest',
                'isGuest' => true,
                'last_activity' => now(),
            ]);
            session()->put('guest_data', [
                'user_id' => $guest->id,
                'session_id' => session()->getId()
            ]);
           
            // session(['guest_user_id' => $guest->id]);
        } else {
            // Update last activity time
            self::where('id', session('guest_data.user_id'))
                ->update(['last_activity' => now()]);
        }
        
        return session('guest_data.user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
