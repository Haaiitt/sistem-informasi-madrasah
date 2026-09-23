<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddGalleryPhotosRequest;
use App\Http\Requests\Admin\StoreGalleryRequest;
use App\Http\Requests\Admin\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Models\GalleryPhoto;
use App\Services\GalleryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __construct(private GalleryService $galleries)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Gallery::class);

        return view('admin.galleries.index', [
            'galleries' => Gallery::withCount('photos')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Gallery::class);

        return view('admin.galleries.create');
    }

    public function store(StoreGalleryRequest $request): RedirectResponse
    {
        $gallery = $this->galleries->create($request->validated(), $request->file('cover'));

        return redirect()->route('admin.galleries.edit', $gallery)
            ->with('status', 'Album berhasil dibuat. Sekarang tambahkan foto.');
    }

    public function edit(Gallery $gallery): View
    {
        $this->authorize('update', $gallery);

        return view('admin.galleries.edit', ['gallery' => $gallery->load('photos')]);
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $this->galleries->update($gallery, $request->validated(), $request->file('cover'));

        return redirect()->route('admin.galleries.index')->with('status', 'Album berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->authorize('delete', $gallery);
        $this->galleries->delete($gallery);

        return redirect()->route('admin.galleries.index')->with('status', 'Album berhasil dihapus.');
    }

    public function storePhotos(AddGalleryPhotosRequest $request, Gallery $gallery): RedirectResponse
    {
        $this->galleries->addPhotos($gallery, $request->file('photos'));

        return back()->with('status', 'Foto berhasil ditambahkan.');
    }

    public function destroyPhoto(GalleryPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $photo->gallery);
        $this->galleries->deletePhoto($photo);

        return back()->with('status', 'Foto berhasil dihapus.');
    }
}
