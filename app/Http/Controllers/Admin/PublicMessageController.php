<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HandlePublicMessageRequest;
use App\Models\PublicMessage;
use App\Services\PublicMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicMessageController extends Controller
{
    public function __construct(private PublicMessageService $messages)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', PublicMessage::class);

        return view('admin.public-messages.index', ['messages' => PublicMessage::latest('created_at')->paginate(20)]);
    }

    public function handle(HandlePublicMessageRequest $request, PublicMessage $publicMessage): RedirectResponse
    {
        $this->messages->markHandled($publicMessage, $request->note, $request->user());

        return back()->with('status', 'Pesan berhasil ditandai ditangani.');
    }
}
