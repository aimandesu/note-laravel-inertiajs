<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return $this->showAll($users);
    }

    public function show(Request $request)
    {
        $guestId = $request->session()->get('guest_data.user_id');
    
        if (!$guestId) {
            return response()->json(['error' => 'No active session found'], 404);
        }
    
        $user = User::find($guestId);
    
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        return response()->json([
            'data' => [
                'username' => $user->username,
                'name' => $user->name,
                'isGuest' => $user->isGuest,
                'last_activity' => $user->last_activity ? strtotime($user->last_activity) : null,
                'lifetime' => config('session.lifetime') * 60,
            ]
        ]);
    }
    

}
