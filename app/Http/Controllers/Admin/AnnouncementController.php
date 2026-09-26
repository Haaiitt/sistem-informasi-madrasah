<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnnouncementRequest;
use App\Models\AdmissionWave;
use App\Models\Announcement;
use App\Services\AnnouncementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function __construct(private AnnouncementService $announcements)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Announcement::class);

        return view('admin.announcements.index', ['announcements' => Announcement::with('wave')->latest('created_at')->paginate(20)]);
    }

    public function create(): View
    {
        $this->authorize('sendBroadcast', Announcement::class);

        return view('admin.announcements.create', ['waves' => AdmissionWave::orderByDesc('opens_at')->get()]);
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $wave = $request->admission_wave_id ? AdmissionWave::find($request->admission_wave_id) : null;
        $this->announcements->send($wave, $request->title, $request->body, $request->user());

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil dikirim.');
    }
}
