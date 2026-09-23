<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Event;
use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'banners' => Banner::active()->get(),
            'posts' => Post::published()->latest('published_at')->take(5)->get(),
            'events' => Event::published()->where('starts_at', '>=', now())->orderBy('starts_at')->take(3)->get(),
        ]);
    }
}
