<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function __construct(private BannerService $banners)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Banner::class);

        return view('admin.banners.index', [
            'banners' => Banner::orderBy('sort_order')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Banner::class);

        return view('admin.banners.create');
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $this->banners->create($request->validated(), $request->file('image'));

        return redirect()->route('admin.banners.index')->with('status', 'Banner berhasil ditambahkan.');
    }

    public function edit(Banner $banner): View
    {
        $this->authorize('update', $banner);

        return view('admin.banners.edit', ['banner' => $banner]);
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $this->banners->update($banner, $request->validated(), $request->file('image'));

        return redirect()->route('admin.banners.index')->with('status', 'Banner berhasil diperbarui.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->authorize('delete', $banner);
        $this->banners->delete($banner);

        return back()->with('status', 'Banner berhasil dihapus.');
    }
}
