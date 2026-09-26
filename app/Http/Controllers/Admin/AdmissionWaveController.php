<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdmissionWaveRequest;
use App\Http\Requests\Admin\UpdateAdmissionWaveRequest;
use App\Models\AcademicYear;
use App\Models\AdmissionWave;
use App\Services\AdmissionWaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AdmissionWaveController extends Controller
{
    public function __construct(private AdmissionWaveService $waves)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', AdmissionWave::class);

        return view('admin.admission-waves.index', [
            'waves' => AdmissionWave::with('academicYear')->latest('opens_at')->paginate(20),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', AdmissionWave::class);

        return view('admin.admission-waves.create', [
            'academicYears' => AcademicYear::orderByDesc('name')->get(),
        ]);
    }

    public function store(StoreAdmissionWaveRequest $request): RedirectResponse
    {
        $this->waves->create($request->validated(), $request->user());

        return redirect()->route('admin.admission-waves.index')->with('status', 'Gelombang berhasil dibuat.');
    }

    public function edit(AdmissionWave $admissionWave): View
    {
        $this->authorize('update', $admissionWave);

        return view('admin.admission-waves.edit', [
            'wave' => $admissionWave,
            'academicYears' => AcademicYear::orderByDesc('name')->get(),
        ]);
    }

    public function update(UpdateAdmissionWaveRequest $request, AdmissionWave $admissionWave): RedirectResponse
    {
        $this->waves->update($admissionWave, $request->validated());

        return redirect()->route('admin.admission-waves.index')->with('status', 'Gelombang berhasil diperbarui.');
    }

    public function open(AdmissionWave $admissionWave): RedirectResponse
    {
        $this->authorize('open', $admissionWave);
        $this->waves->openManually($admissionWave, Auth::user());

        return back()->with('status', 'Gelombang dibuka.');
    }

    public function close(AdmissionWave $admissionWave): RedirectResponse
    {
        $this->authorize('close', $admissionWave);
        $this->waves->closeManually($admissionWave, Auth::user());

        return back()->with('status', 'Gelombang ditutup.');
    }
}
