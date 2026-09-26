<?php

namespace App\Http\Controllers\Ppdb;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\ApplicantDocument;
use App\Services\ApplicantDocumentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicantDocumentController extends Controller
{
    public function __construct(private ApplicantDocumentService $documents)
    {
    }

    public function store(Request $request, Applicant $applicant): RedirectResponse
    {
        $this->authorize('update', $applicant);

        $request->validate([
            'type' => ['required', 'in:kk,akta_kelahiran,pas_foto,keterangan_tk_ra,surat_pindah'],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $this->documents->upload($applicant, $request->string('type'), $request->file('file'));

        return back()->with('status', 'Berkas berhasil diunggah.');
    }

    public function show(ApplicantDocument $document): StreamedResponse
    {
        $this->authorize('view', $document->applicant);

        return Storage::disk('local')->response($document->storage_path, $document->original_name);
    }
}
