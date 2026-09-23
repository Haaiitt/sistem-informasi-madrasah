@extends('layouts.public')
@section('title', $page->title)

@section('content')
<article class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-semibold text-text">{{ $page->title }}</h1>
    <div class="prose prose-sm max-w-none mt-6 text-text">{!! $page->body !!}</div>
</article>
@endsection
