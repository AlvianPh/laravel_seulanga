<?php

namespace App\Http\Controllers;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Http\Requests\ReviewTenantDocumentRequest;
use App\Models\Tenant;
use App\Models\TenantDocument;
use App\Services\TenantDocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TenantDocumentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TenantDocumentService $documentService
    ) {}

    /**
     * Menampilkan daftar semua dokumen berkas penghuni untuk staff.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', TenantDocument::class);

        $query = TenantDocument::with(['tenant', 'uploader', 'verifier'])->latest();

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%")
                    ->orWhereHas('tenant', function ($t) use ($search) {
                        $t->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $documents = $query->paginate(15)->withQueryString();
        $tenants = Tenant::orderBy('name')->get();
        $types = DocumentType::cases();
        $statuses = DocumentStatus::cases();

        return view('documents.index', compact('documents', 'tenants', 'types', 'statuses'));
    }

    /**
     * Menampilkan detail berkas dokumen dan form aksi verifikasi.
     */
    public function show(TenantDocument $document): View
    {
        $this->authorize('view', $document);

        $document->load(['tenant.currentContract.room', 'uploader', 'verifier']);

        return view('documents.show', compact('document'));
    }

    /**
     * Memproses verifikasi atau penolakan dokumen penghuni.
     */
    public function review(ReviewTenantDocumentRequest $request, TenantDocument $document): RedirectResponse
    {
        $this->authorize('verify', $document);

        $validated = $request->validated();
        $user = $request->user();

        if ($validated['action'] === 'verify') {
            $this->documentService->verifyDocument($document, $user);
            $message = "Dokumen '{$document->title}' berhasil diverifikasi.";
        } else {
            $this->documentService->rejectDocument($document, $user, $validated['rejection_reason']);
            $message = "Dokumen '{$document->title}' berhasil ditolak.";
        }

        return redirect()->route('documents.show', $document)
            ->with('success', $message);
    }

    /**
     * Mengunduh berkas fisik dokumen.
     */
    public function download(TenantDocument $document): StreamedResponse
    {
        $this->authorize('download', $document);

        return $this->documentService->download($document);
    }

    /**
     * Pratinjau berkas dokumen langsung di tab peramban.
     */
    public function preview(TenantDocument $document): BinaryFileResponse
    {
        $this->authorize('view', $document);

        return $this->documentService->stream($document);
    }

    /**
     * Menghapus dokumen penghuni dan berkas fisiknya.
     */
    public function destroy(TenantDocument $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        $this->documentService->deleteDocument($document);

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}
