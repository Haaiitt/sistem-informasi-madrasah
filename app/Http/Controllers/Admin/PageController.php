<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePageRequest;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(private PageService $pages)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Page::class);

        return view('admin.pages.index', [
            'pages' => Page::with('author')->orderBy('sort_order')->orderBy('title')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Page::class);

        return view('admin.pages.create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $this->pages->create($request->validated(), $request->user()->id);

        return redirect()->route('admin.pages.index')->with('status', 'Halaman berhasil dibuat.');
    }

    public function edit(Page $page): View
    {
        $this->authorize('update', $page);

        return view('admin.pages.edit', ['page' => $page]);
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $this->pages->update($page, $request->validated());

        return redirect()->route('admin.pages.index')->with('status', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->authorize('delete', $page);
        $this->pages->delete($page);

        return back()->with('status', 'Halaman berhasil dihapus.');
    }
}
