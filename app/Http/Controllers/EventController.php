<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        return view('public.events.index', [
            'events' => Event::published()->orderBy('starts_at', 'desc')->paginate(10),
        ]);
    }
}
