<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        return view('public.galleries.index', [
            'galleries' => Gallery::where('is_published', true)->withCount('photos')->latest()->paginate(12),
        ]);
    }

    public function show(int $id): View
    {
        $gallery = Gallery::where('is_published', true)->with('photos')->findOrFail($id);

        return view('public.galleries.show', ['gallery' => $gallery]);
    }
}
