<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::with(['customer','supplier'])
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no' => 'required|unique:invoices',
            'type' => 'required|in:in,out',
            'tgl' => 'required|date',
            'grand_total' => 'required|numeric'
        ]);

        $invoice = Invoice::create($request->all());

        return response()->json($invoice);
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load('payments.payment');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

