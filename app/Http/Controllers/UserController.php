<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('settings.users.index', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak bisa mengubah role diri sendiri.');
        }

        $request->validate([
            'role' => 'required|in:admin,staff'
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role {$user->name} berhasil diubah ke {$request->role}.");
    }
}
