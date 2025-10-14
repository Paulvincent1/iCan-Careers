<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CloudinaryFileUploadService;
use CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class ProcessInvoicePdf implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $employer,
        public string $externalId,
        public string $dueDate,
        public string $description,
        public array $items,
        public float $xenditTransactionFee,
        public float $vatTransactionFee,
        public int $totalAmount,
        public string $invoiceUrl,
        )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Pdf::view('pdf.invoice', [
                'invoiceId' => $this->externalId,
                'dueDate' => $this->dueDate,
                'description' => $this->description,
                'employer' => $this->employer,
                'items' => $this->items,
                'xenditTransactionFee' => $this->xenditTransactionFee,
                'vatTransactionFee' => $this->vatTransactionFee,
                'totalAmount' => $this->totalAmount + $this->xenditTransactionFee + $this->vatTransactionFee,
                'invoiceUrl' => $this->invoiceUrl
            ])->disk('public')->save('/invoices/' . $this->externalId . '.pdf');

            $tempPath = Storage::disk('public')->path('/invoices/' . $this->externalId . '.pdf');

            $cloudinary = app(CloudinaryFileUploadService::class);

            $cloudinary->uploadFile(
                path: $tempPath,
                folder: 'invoices',
                uploadPreset: 'invoices'
            );

            Log::info("✅ Invoice job completed successfully for ID: {$this->externalId}");

        } catch (\Throwable $e) {
            Log::error("❌ Invoice job failed for ID: {$this->externalId}", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Important: Rethrow the exception so Laravel marks the job as failed
            throw $e;
        }
    }
}
