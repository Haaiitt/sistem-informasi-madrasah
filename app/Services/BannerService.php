<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    public function create(array $data, UploadedFile $image): Banner
    {
        return Banner::create([
            'title' => $data['title'] ?? null,
            'image_path' => $image->store('banners', 'public'),
            'link_url' => $data['link_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(Banner $banner, array $data, ?UploadedFile $image): Banner
    {
        $imagePath = $banner->image_path;
        if ($image) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = $image->store('banners', 'public');
        }

        $banner->update([
            'title' => $data['title'] ?? null,
            'image_path' => $imagePath,
            'link_url' => $data['link_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return $banner;
    }

    public function delete(Banner $banner): void
    {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete(); // hapus permanen, sesuai database.md
    }
}
