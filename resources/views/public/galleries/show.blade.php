@extends('layouts.public')
@section('title', $gallery->title)

@section('content')
<a href="{{ route('galleries.index') }}" class="text-sm text-muted hover:text-text transition-colors">&larr; Galeri Foto</a>
<h1 class="text-xl font-semibold text-text mt-3">{{ $gallery->title }}</h1>
@if ($gallery->description)<p class="text-sm text-secondary mt-2">{{ $gallery->description }}</p>@endif

<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-6">
    @foreach ($gallery->photos as $photo)
        <img src="{{ Storage::url($photo->image_path) }}" alt="{{ $photo->caption }}" class="w-full aspect-square object-cover rounded-md border border-border">
    @endforeach
</div>
@endsection
