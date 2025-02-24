<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
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
        
        Pdf::view('pdf.invoice',[
            'invoiceId' => $this->externalId,
            'dueDate' => $this->dueDate,
            'description' => $this->description,
            'employer' => $this->employer,
            'items' =>  $this->items,
            'xenditTransactionFee' => $this->xenditTransactionFee,
            'vatTransactionFee' => $this->vatTransactionFee,
            'totalAmount' => $this->totalAmount + $this->xenditTransactionFee + $this->vatTransactionFee,
            'invoiceUrl' => $this->invoiceUrl
        ])->disk('public')->save('/invoices/' . $this->externalId . '.pdf');
    }
}
