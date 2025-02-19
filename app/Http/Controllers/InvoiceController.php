<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

use function Illuminate\Log\log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function webhook(Request $request){

        if($request->header('X-CALLBACK-TOKEN') === env('XENDIT_WEBHOOK_KEY')){

            $invoice = Invoice::where('external_id', $request['external_id'])->with(['worker.balance'])->first();

            $invoice->update([
                'status' => $request['status']
            ]);

            $invoice->worker->balance()->increment('unsettlement', $invoice->amount);

        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
