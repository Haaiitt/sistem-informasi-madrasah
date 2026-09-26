<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ppdb\ReplyMessageRequest;
use App\Http\Requests\Ppdb\StoreConversationRequest;
use App\Models\Applicant;
use App\Models\Conversation;
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
        $user = auth()->user();

        // FR-MSG-08: pengumuman dianggap terbaca begitu tampil di kotak masuk.
        \Illuminate\Support\Facades\DB::table('announcement_recipients')
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('ppdb.conversations.index', [
            'conversations' => $user->conversations()->latest('last_message_at')->get(),
            'announcements' => $user->announcements()->latest('created_at')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('startAsGuardian', Conversation::class);

        return view('ppdb.conversations.create', [
            'applicants' => auth()->user()->applicants()->get(['id', 'full_name']),
        ]);
    }

    public function store(StoreConversationRequest $request): RedirectResponse
    {
        $applicant = $request->applicant_id ? Applicant::find($request->applicant_id) : null;
        $conversation = $this->conversations->startFromGuardian($request->user(), $applicant, $request->subject, $request->body);

        return redirect()->route('ppdb.conversations.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorize('view', $conversation);
        $this->conversations->markReadForGuardian($conversation);

        return view('ppdb.conversations.show', ['conversation' => $conversation->load('messages.sender')]);
    }

    public function reply(ReplyMessageRequest $request, Conversation $conversation): RedirectResponse
    {
        $this->conversations->reply($conversation, $request->user(), 'peserta', $request->body);

        return back();
    }
}
