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
            Log::info("Step 1: Starting PDF generation for {$this->externalId}");

            Pdf::view('pdf.invoice',  [
                'invoiceId' => $this->externalId,
                'dueDate' => $this->dueDate,
                'description' => $this->description,
                'employer' => $this->employer,
                'items' => $this->items,
                'xenditTransactionFee' => $this->xenditTransactionFee,
                'vatTransactionFee' => $this->vatTransactionFee,
                'totalAmount' => $this->totalAmount + $this->xenditTransactionFee + $this->vatTransactionFee,
                'invoiceUrl' => $this->invoiceUrl
            ])
                ->disk('public')
                ->save("/invoices/{$this->externalId}.pdf");

            Log::info("Step 2: PDF saved for {$this->externalId}");

            $tempPath = Storage::disk('public')->path("/invoices/{$this->externalId}.pdf");

            Log::info("Step 3: Uploading to Cloudinary for {$this->externalId}");

            app(CloudinaryFileUploadService::class)->uploadFile(
                path: $tempPath,
                folder: 'invoices',
                uploadPreset: 'invoices'
            );

            Log::info("✅ Job complete: {$this->externalId}");

        } catch (\Throwable $e) {
            Log::error("❌ Job failed for {$this->externalId}", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

}
