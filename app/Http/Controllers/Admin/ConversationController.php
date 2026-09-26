<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffConversationRequest;
use App\Http\Requests\Ppdb\ReplyMessageRequest;
use App\Models\Applicant;
use App\Models\Conversation;
use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function __construct(private ConversationService $conversations)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', \App\Models\Announcement::class);

        return view('admin.conversations.index', [
            'conversations' => Conversation::with('user')->latest('last_message_at')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('startAsStaff', Conversation::class);

        return view('admin.conversations.create', [
            'participants' => User::whereHas('roles', fn ($q) => $q->where('name', 'calon_siswa'))->get(['id', 'name', 'username']),
        ]);
    }

    public function store(StoreStaffConversationRequest $request): RedirectResponse
    {
        $participant = User::findOrFail($request->user_id);
        $applicant = $request->applicant_id ? Applicant::find($request->applicant_id) : null;
        $conversation = $this->conversations->startFromStaff($participant, $applicant, $request->subject, $request->body, $request->user());

        return redirect()->route('admin.conversations.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorize('view', $conversation);
        $this->conversations->markReadForStaff($conversation);

        return view('admin.conversations.show', ['conversation' => $conversation->load('messages.sender', 'user')]);
    }

    public function reply(ReplyMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        $this->conversations->reply($conversation, $request->user(), 'admin', $request->body);

        return back();
    }
}
