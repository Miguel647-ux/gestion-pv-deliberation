<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (! method_exists(User::class, 'program')) {
            User::resolveRelationUsing('program', function (User $user) {
                return $user->belongsTo(Program::class, 'program_id');
            });
        }

        $users = User::with('program')->orderBy('id')->get();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }
}
