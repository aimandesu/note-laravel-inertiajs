<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $users = Note::all();
        // return $this->showAll($users);

        // $guestId = User::getGuestId();
        $guestId = $request->session()->get('guest_data.user_id');

        if(!$guestId){
            Artisan::call('guests:cleanup');
        }

        $notes = Note::where('user_id', $guestId)->get();
        return $this->showAll($notes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|string',
        ];

        $this->validate($request, $rules);

        $note = Note::create([
            'user_id' => User::getGuestId(),
            'title' => $request->title,
            'description' => $request->description,
            'images' => $request->images,
        ]);



        return $this->showOne($note, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
