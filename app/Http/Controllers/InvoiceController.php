<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $faktur = Invoice::with('customer')
            ->where('type', 'penjualan')
            ->latest()
            ->get();

        return view('penjualan.faktur.index', compact('faktur'));
    }

    public function create()
    {
        return view('penjualan.faktur.create');
    }

    public function store(Request $request)
    {
        Invoice::create([
            'no' => $request->no_faktur,
            'no_so' => $request->no_po,
            'tgl' => $request->tgl_faktur,
            'dpp' => $request->dpp,
            'ppn' => $request->ppn,
            'grand_total' => $request->grand_total,
            'jatuh_tempo' => $request->jatuh_tempo,
            'status' => 'unpaid',
            'paid' => 0,
            'type' => 'penjualan',
            'customer_id' => $request->customer_id,
        ]);

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
