<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(private EventService $events)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Event::class);

        return view('admin.events.index', [
            'events' => Event::with('author')->orderBy('starts_at', 'desc')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        return view('admin.events.create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $this->events->create($request->validated(), $request->user()->id);

        return redirect()->route('admin.events.index')->with('status', 'Agenda berhasil dibuat.');
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('admin.events.edit', ['event' => $event]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $this->events->update($event, $request->validated());

        return redirect()->route('admin.events.index')->with('status', 'Agenda berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);
        $this->events->delete($event);

        return back()->with('status', 'Agenda berhasil dihapus.');
    }
}
