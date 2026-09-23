<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePostRequest;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Post;
use App\Models\PostCategory;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(private PostService $posts)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Post::class);

        return view('admin.posts.index', [
            'posts' => Post::with(['category', 'author'])->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Post::class);

        return view('admin.posts.create', ['categories' => PostCategory::orderBy('name')->get()]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->posts->create($request->validated(), $request->user()->id);

        return redirect()->route('admin.posts.index')->with('status', 'Konten berhasil dibuat.');
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        return view('admin.posts.edit', [
            'post' => $post,
            'categories' => PostCategory::orderBy('name')->get(),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->posts->update($post, $request->validated());

        return redirect()->route('admin.posts.index')->with('status', 'Konten berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);
        $this->posts->delete($post);

        return back()->with('status', 'Konten berhasil dihapus.');
    }
}
