<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\User;

class GalleryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('galleries.view');
    }

    public function view(User $user, Gallery $gallery): bool
    {
        return $user->hasPermission('galleries.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('galleries.create');
    }

    public function update(User $user, Gallery $gallery): bool
    {
        return $user->hasPermission('galleries.update');
    }

    public function delete(User $user, Gallery $gallery): bool
    {
        return $user->hasPermission('galleries.delete');
    }
}
