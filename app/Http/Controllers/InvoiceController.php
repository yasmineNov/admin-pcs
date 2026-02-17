<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\Invoice;
use App\Models\OrderDetail;
use App\Models\Barang;
use App\Models\InvoiceDetail;

class InvoiceController extends Controller
{
    // ===========================
    // INDEX
    // ===========================

    public function indexMasuk()
    {
        $invoices = Invoice::where('type', 'masuk')
            ->with('details.orderDetail.barang', 'order.supplier')
            ->latest()->get();

        return view('pembelian.invoice.index', compact('invoices'));
    }

    public function indexKeluar()
    {
        $invoices = Invoice::where('type', 'keluar')
            ->with('details.orderDetail.barang', 'order.customer')
            ->latest()->get();

        return view('penjualan.invoice.index', compact('invoices'));
    }

    // ===========================
    // CREATE
    // ===========================

    public function createMasuk()
    {
        $orders = Orders::where('type', 'purchase')
            ->where('status', '!=', 'draft')
            ->with('details.barang', 'supplier')
            ->get();

        return view('pembelian.invoice.create', compact('orders'));
    }

    public function createKeluar()
    {
        $orders = Orders::where('type', 'sales')
            ->where('status', '!=', 'draft')
            ->with('details.barang', 'customer')
            ->get();

        return view('penjualan.invoice.create', compact('orders'));
    }

    // ===========================
    // STORE
    // ===========================

    public function store(Request $request, $type)
    {
        $request->validate([
            'no' => 'required|unique:invoices,no',
            'tgl' => 'required|date',
            'alamat_kirim' => 'required',
            'order_id' => 'nullable|exists:orders,id',
            'details.*.order_detail_id' => 'required|exists:order_details,id',
            'details.*.keterangan' => 'nullable|string'
        ]);

        $invoice = Invoice::create([
            'no' => $request->no,
            'type' => $type,
            'tgl' => $request->tgl,
            'order_id' => $request->order_id,
            // 'harga_jual' => $request->harga_jual,
            'alamat_kirim' => $request->alamat_kirim,
        ]);

        foreach ($request->details as $item) {
            $detail = $invoice->details()->create([
                'order_detail_id' => $item['order_detail_id'],
                'keterangan' => $item['keterangan'] ?? null
            ]);

            // Update stok
            $barang = $detail->orderDetail->barang;
            $qty = $detail->orderDetail->qty;
            if ($type == 'masuk') {
                $barang->stok += $qty;
            } else {
                $barang->stok -= $qty;
            }
            $barang->save();
        }

        $route = $type == 'masuk' ? 'pembelian.invoice.index' : 'penjualan.invoice.index';
        return redirect()->route($route)->with('success', 'Invoice berhasil dibuat.');
    }

    // ===========================
    // EDIT
    // ===========================

    public function edit(Invoice $invoice)
    {
        if ($invoice->type == 'masuk') {
            $orders = Orders::where('type', 'purchase')
                ->where('status', '!=', 'draft')
                ->with('details.barang', 'supplier')
                ->get();
            $invoice->load('details.orderDetail.barang');

            return view('pembelian.invoice.edit', compact('invoice', 'orders'));
        } else {
            $orders = Orders::where('type', 'sales')
                ->where('status', '!=', 'draft')
                ->with('details.barang', 'customer')
                ->get();
            $invoice->load('details.orderDetail.barang');

            return view('penjualan.invoice.edit', compact('invoice', 'orders'));
        }
    }

    // ===========================
    // SHOW
    // ===========================

    public function show(Invoice $invoice)
    {
        $invoice->load('details.orderDetail.barang', 'order');

        if ($invoice->type == 'masuk') {
            return view('pembelian.invoice.show', compact('invoice'));
        } else {
            return view('penjualan.invoice.show', compact('invoice'));
        }
    }

    // ===========================
    // UPDATE
    // ===========================

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'tgl' => 'required|date',
            'details.*.order_detail_id' => 'required|exists:order_details,id',
        ]);

        // Rollback stok lama
        foreach ($invoice->details as $d) {
            $barang = $d->orderDetail->barang;
            $qty = $d->orderDetail->qty;
            if ($invoice->type == 'masuk') {
                $barang->stok -= $qty;
            } else {
                $barang->stok += $qty;
            }
            $barang->save();
        }

        $invoice->update([
            'tgl' => $request->tgl,
            'keterangan' => $request->keterangan,
            'alamat_kirim' => $request->alamat_kirim
        ]);

        // Hapus detail lama
        $invoice->details()->delete();

        // Tambah detail baru & update stok
        foreach ($request->details as $item) {
            $detail = $invoice->details()->create([
                'order_detail_id' => $item['order_detail_id'],
                'keterangan' => $item['keterangan'] ?? null
            ]);

            $barang = $detail->orderDetail->barang;
            $qty = $detail->orderDetail->qty;
            if ($invoice->type == 'masuk') {
                $barang->stok += $qty;
            } else {
                $barang->stok -= $qty;
            }
            $barang->save();
        }

        $route = $invoice->type == 'masuk' ? 'pembelian.invoice.index' : 'penjualan.invoice.index';
        return redirect()->route($route)->with('success', 'Invoice berhasil diupdate.');
    }

    // ===========================
    // DELETE
    // ===========================

    public function destroy(Invoice $invoice)
    {
        // Rollback stok
        foreach ($invoice->details as $d) {
            $barang = $d->orderDetail->barang;
            $qty = $d->orderDetail->qty;
            if ($invoice->type == 'masuk') {
                $barang->stok -= $qty;
            } else {
                $barang->stok += $qty;
            }
            $barang->save();
        }

        $invoice->delete();

        $route = $invoice->type == 'masuk' ? 'pembelian.invoice.index' : 'penjualan.invoice.index';
        return redirect()->route($route)->with('success', 'Invoice berhasil dihapus.');
    }

    // ===========================
    // GET ORDER DETAILS
    // ===========================

    public function getOrderDetail($id)
    {
        $order = Orders::with('details.barang')->findOrFail($id);

        $items = $order->details->map(function ($detail) {
            return [
                'barang_id' => $detail->barang->id,
                'nama_barang' => $detail->barang->nama_barang,
                'qty' => $detail->qty,
            ];
        });

        return response()->json($items);
    }
}
