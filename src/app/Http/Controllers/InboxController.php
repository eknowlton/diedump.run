<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function __invoke(Request $request)
    {
        return inertia('inbox/index')->with([
            'inbox' => Task::inboxFor($request->user())->limit(10)->get(),
            'twoMinute' => Task::twoMinuteFor($request->user())->limit(10)->get(),
        ]);
    }
}
