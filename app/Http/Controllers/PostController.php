<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::published()
            ->with('category')
            ->when($request->filled('kategori'), fn ($q) => $q->whereHas(
                'category',
                fn ($c) => $c->where('slug', $request->query('kategori'))
            ))
            ->when($request->filled('q'), fn ($q) => $q->where(
                fn ($sub) => $sub->where('title', 'like', '%'.$request->query('q').'%')
                    ->orWhere('excerpt', 'like', '%'.$request->query('q').'%')
            ))
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('public.posts.index', [
            'posts' => $posts,
            'categories' => PostCategory::orderBy('name')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::published()->with(['category', 'author'])->where('slug', $slug)->firstOrFail();

        return view('public.posts.show', ['post' => $post]);
    }
}
