<?php

namespace App\Http\Controllers\Portal;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\StatusPembayaran;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreTenantDocumentRequest;
use App\Models\TenantDocument;
use App\Services\TenantDocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TenantDocumentService $documentService
    ) {}

    /**
     * Menampilkan daftar dokumen dan berkas milik penghuni.
     */
    public function index(Request $request): View
    {
        $tenant = $request->user()->tenant;

        if (! $tenant) {
            return view('portal.documents.index', [
                'tenant' => null,
                'documents' => collect(),
                'activeContract' => null,
                'verifiedPayments' => collect(),
                'types' => DocumentType::cases(),
                'statuses' => DocumentStatus::cases(),
            ]);
        }

        $query = $tenant->documents()->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $documents = $query->paginate(10)->withQueryString();
        $activeContract = $tenant->currentContract();
        $verifiedPayments = $tenant->payments()
            ->where('status', StatusPembayaran::Verified)
            ->with(['invoice.room', 'paymentMethod'])
            ->latest('payment_date')
            ->take(5)
            ->get();

        $types = DocumentType::cases();
        $statuses = DocumentStatus::cases();

        return view('portal.documents.index', compact('tenant', 'documents', 'activeContract', 'verifiedPayments', 'types', 'statuses'));
    }

    /**
     * Form unggah berkas dokumen baru oleh penghuni.
     */
    public function create(Request $request): View
    {
        $tenant = $request->user()->tenant;
        $allowedTypes = DocumentType::allowedForTenantUpload();

        return view('portal.documents.create', compact('tenant', 'allowedTypes'));
    }

    /**
     * Menyimpan berkas dokumen baru ke penyimpanan privat.
     */
    public function store(StoreTenantDocumentRequest $request): RedirectResponse
    {
        $tenant = $request->user()->tenant;
        $user = $request->user();

        $this->documentService->createDocument(
            tenant: $tenant,
            uploader: $user,
            data: $request->validated(),
            file: $request->file('file')
        );

        return redirect()->route('portal.documents.index')
            ->with('status', 'Dokumen berhasil diunggah dan sedang menunggu verifikasi.');
    }

    /**
     * Menampilkan detail berkas dokumen.
     */
    public function show(TenantDocument $document): View
    {
        $this->authorize('view', $document);

        $document->load(['tenant', 'uploader', 'verifier']);

        return view('portal.documents.show', compact('document'));
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
     * Menghapus dokumen yang masih berstatus pending.
     */
    public function destroy(TenantDocument $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        $this->documentService->deleteDocument($document);

        return redirect()->route('portal.documents.index')
            ->with('status', 'Dokumen berhasil dihapus.');
    }
}
