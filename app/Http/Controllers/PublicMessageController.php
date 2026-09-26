<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicMessageRequest;
use App\Services\PublicMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicMessageController extends Controller
{
    public function __construct(private PublicMessageService $messages)
    {
    }

    public function create(): View
    {
        return view('public.contact');
    }

    public function store(PublicMessageRequest $request): RedirectResponse
    {
        $this->messages->submit($request->validated());

        return back()->with('status', 'Pesan Anda berhasil dikirim. Kami akan menghubungi Anda lewat kontak yang diisi.');
    }
}
