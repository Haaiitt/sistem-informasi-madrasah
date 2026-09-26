<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionWave;
use App\Models\Applicant;
use App\Services\ApplicantReviewService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\ApplicantConversionService;
use App\Exports\ApplicantsExport;
use Maatwebsite\Excel\Facades\Excel;

class ApplicantReviewController extends Controller
{
    public function __construct(private ApplicantReviewService $review)
    {
    }

    public function index(AdmissionWave $wave): View
    {
        $this->authorize('viewAsStaff', Applicant::class);

        return view('admin.applicants.index', [
            'wave' => $wave,
            'applicants' => $wave->applicants()->with('user')->latest('submitted_at')->paginate(20),
        ]);
    }

    public function show(Applicant $applicant): View
    {
        $this->authorize('viewAsStaff', Applicant::class);

        return view('admin.applicants.show', ['applicant' => $applicant->load(['guardians', 'documents', 'statusHistories.changer', 'wave'])]);
    }

    public function verify(Request $request, Applicant $applicant): RedirectResponse
    {
        $this->authorize('verify', $applicant);
        $request->validate(['result' => ['required', 'in:terverifikasi,perlu_perbaikan,ditolak'], 'note' => ['nullable', 'string']]);

        $this->review->verify($applicant, $request->string('result'), $request->input('note'), $request->user());

        return back()->with('status', 'Status verifikasi berhasil diperbarui.');
    }

    public function decide(Request $request, Applicant $applicant): RedirectResponse
    {
        $this->authorize('decide', $applicant);
        $request->validate(['result' => ['required', 'in:diterima,tidak_diterima']]);

        $this->review->decide($applicant, $request->string('result'), $request->user());

        return back()->with('status', 'Hasil seleksi berhasil ditetapkan.');
    }

    public function reviseDecision(Request $request, Applicant $applicant): RedirectResponse
    {
        $this->authorize('decide', $applicant);
        $request->validate(['result' => ['required', 'in:diterima,tidak_diterima'], 'reason' => ['required', 'string']]);

        $this->review->reviseDecisionAfterAnnouncement($applicant, $request->string('result'), $request->string('reason'), $request->user());

        return back()->with('status', 'Hasil seleksi berhasil direvisi.');
    }

    public function recordReenrollment(Request $request, Applicant $applicant): RedirectResponse
    {
        $this->authorize('recordReenrollment', $applicant);
        $request->validate(['result' => ['required', 'in:daftar_ulang,mengundurkan_diri']]);

        $this->review->recordReenrollment($applicant, $request->string('result'), $request->user());

        return back()->with('status', 'Status berhasil dicatat.');
    }

    public function announce(AdmissionWave $wave): RedirectResponse
    {
        $this->authorize('publishResults', $wave);

        $pending = $this->review->announceResults($wave);
        $message = 'Hasil gelombang berhasil diumumkan.';
        if ($pending > 0) {
            $message .= " Perhatian: masih ada {$pending} pendaftar yang belum ditetapkan hasilnya.";
        }

        return back()->with('status', $message);
    }

    public function convert(Applicant $applicant, ApplicantConversionService $conversion): RedirectResponse
    {
        $this->authorize('convert', $applicant);

        $conversion->convert($applicant, auth()->user());

        return back()->with('status', 'Pendaftar berhasil dikonversi menjadi data siswa.');
    }

    public function export(AdmissionWave $wave)
    {
        $this->authorize('export', Applicant::class);

        return Excel::download(new ApplicantsExport($wave), "pendaftar-{$wave->name}.xlsx");
    }
}
