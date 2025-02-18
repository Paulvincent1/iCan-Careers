<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class InvoiceUpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:invoice-update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of invoice if its settled and ready to payout.';

    /**
     * Execute the console command.
     */
    public function handle(InvoiceService $invoiceService)
    {
        $invoiceService->updateInvoiceStatus();
    }
}
