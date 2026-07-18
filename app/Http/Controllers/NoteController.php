<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'noteable_id' => 'required',
            'noteable_type' => 'required',
            'content' => 'required|string'
        ]);

        Note::create([
            'user_id' => auth()->id(),
            'noteable_id' => $request->noteable_id,
            'noteable_type' => $request->noteable_type,
            'content' => $request->content
        ]);

        return back()->with('success', 'Note added.');
    }

    public function destroy(Note $note)
    {
        if (auth()->id() !== $note->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }
        $note->delete();
        return back()->with('success', 'Note deleted.');
    }
}
