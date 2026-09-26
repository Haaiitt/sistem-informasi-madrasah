<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ppdb\StoreApplicantRequest;
use App\Http\Requests\Ppdb\UpdateApplicantRequest;
use App\Models\AdmissionWave;
use App\Models\Applicant;
use App\Services\ApplicantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\ApplicantDocumentService;
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicantController extends Controller
{
    public function __construct(private ApplicantService $applicants)
    {
    }

    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        return view('ppdb.applicants.index', [
            'applicants' => $user->applicants()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Applicant::class);

        return view('ppdb.applicants.create', ['waveOpen' => AdmissionWave::where('status', 'dibuka')->exists()]);
    }

    public function store(StoreApplicantRequest $request): RedirectResponse
    {
        if (! AdmissionWave::where('status', 'dibuka')->exists()) {
            return back()->withErrors(['type' => 'Tidak ada gelombang dibuka saat ini. Draft tidak dapat dibuat.'])->withInput();
        }

        $applicant = $this->applicants->createDraft($request->user(), $request->validated());

        return redirect()->route('ppdb.applicants.edit', $applicant)->with('status', 'Draft berhasil disimpan.');
    }

    public function edit(Applicant $applicant): View
    {
        $this->authorize('view', $applicant);

        return view('ppdb.applicants.edit', ['applicant' => $applicant->load('guardians')]);
    }

    public function update(UpdateApplicantRequest $request, Applicant $applicant): RedirectResponse
    {
        $this->applicants->updateDraft($applicant, $request->validated());

        return redirect()->route('ppdb.applicants.edit', $applicant)->with('status', 'Draft berhasil disimpan.');
    }

    public function submit(Applicant $applicant, ApplicantDocumentService $documents): RedirectResponse
    {
        $this->authorize('update', $applicant);

        $this->applicants->submit($applicant, $documents);

        return redirect()->route('ppdb.applicants.receipt', $applicant)->with('status', 'Pendaftaran berhasil dikirim.');
    }

    public function receipt(Applicant $applicant)
    {
        $this->authorize('view', $applicant);

        abort_unless($applicant->registration_number, 404);

        $pdf = Pdf::loadView('ppdb.applicants.receipt', ['applicant' => $applicant->load('guardians')]);

        return $pdf->stream("bukti-pendaftaran-{$applicant->registration_number}.pdf");
    }

    // FR-PPDB-18 / BR-04 tahap 1: pemberitahuan dini, respons generik.
    public function checkNisn(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['nisn' => ['required', 'digits:10']]);

        $available = $this->applicants->nisnLooksAvailable(
            $request->string('nisn'),
            \App\Models\AcademicYear::where('is_active', true)->value('id'),
            $request->integer('applicant_id') ?: null
        );

        return response()->json([
            'message' => $available ? 'NISN dapat dipakai.' : 'NISN tidak dapat dipakai.',
        ]);
    }
}
