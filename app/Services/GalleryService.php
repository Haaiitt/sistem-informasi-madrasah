<?php

namespace App\Services;

use App\Models\Gallery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GalleryService
{
    public function create(array $data, ?UploadedFile $cover): Gallery
    {
        return Gallery::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'cover_image_path' => $cover ? $cover->store('galleries/covers', 'public') : null,
            'is_published' => $data['is_published'] ?? false,
        ]);
    }

    public function update(Gallery $gallery, array $data, ?UploadedFile $cover): Gallery
    {
        $coverPath = $gallery->cover_image_path;
        if ($cover) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }
            $coverPath = $cover->store('galleries/covers', 'public');
        }

        $gallery->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'cover_image_path' => $coverPath,
            'is_published' => $data['is_published'] ?? false,
        ]);

        return $gallery;
    }

    // gallery_photos: hapus permanen berikut berkasnya (database.md), bukan soft delete.
    public function addPhotos(Gallery $gallery, array $photos): void
    {
        $maxOrder = $gallery->photos()->max('sort_order') ?? 0;

        foreach ($photos as $i => $photo) {
            $gallery->photos()->create([
                'image_path' => $photo->store('galleries/photos', 'public'),
                'sort_order' => $maxOrder + $i + 1,
            ]);
        }
    }

    public function deletePhoto(\App\Models\GalleryPhoto $photo): void
    {
        Storage::disk('public')->delete($photo->image_path);
        $photo->delete();
    }

    public function delete(Gallery $gallery): void
    {
        // Foto ikut terhapus permanen (FK CASCADE di DB), tapi berkas fisiknya harus dibersihkan manual.
        foreach ($gallery->photos as $photo) {
            Storage::disk('public')->delete($photo->image_path);
        }
        if ($gallery->cover_image_path) {
            Storage::disk('public')->delete($gallery->cover_image_path);
        }
        $gallery->delete();
    }
}
