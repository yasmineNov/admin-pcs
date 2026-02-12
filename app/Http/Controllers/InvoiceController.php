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

        return view('penjualan.faktur.index', compact('faktur'));
    }

    public function create()
    {
        return view('faktur.create');
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

        return redirect()->route('penjualan.faktur.index')
            ->with('success', 'Faktur berhasil dibuat');
    }

    public function edit($id)
    {
        $faktur = Invoice::findOrFail($id);
        return view('penjualan.faktur.edit', compact('faktur'));
    }

    public function update(Request $request, $id)
    {
        $faktur = Invoice::findOrFail($id);

        $faktur->update([
            'no' => $request->no_faktur,
            'no_so' => $request->no_po,
            'tgl' => $request->tgl_faktur,
            'dpp' => $request->dpp,
            'ppn' => $request->ppn,
            'grand_total' => $request->grand_total,
            'jatuh_tempo' => $request->jatuh_tempo,
            'customer_id' => $request->customer_id,
        ]);

        return redirect()->route('penjualan.faktur.index')
            ->with('success', 'Faktur berhasil diupdate');
    }

    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();

        return redirect()->route('penjualan.faktur.index')
            ->with('success', 'Faktur berhasil dihapus');
    }
}
