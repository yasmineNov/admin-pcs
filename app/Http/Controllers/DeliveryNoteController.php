<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDetail;
use App\Models\Orders;
use App\Models\OrderDetail;
use App\Models\Barang;

class DeliveryNoteController extends Controller
{
    // ===========================
    // INDEX
    // ===========================

    // DN Pembelian (Masuk)
    public function indexMasuk()
    {
        $deliveryNotes = DeliveryNote::where('type', 'masuk')
            ->with('details.orderDetail.barang', 'order.supplier')
            ->latest()->get();

        return view('pembelian.delivery_note.index', compact('deliveryNotes'));
    }

    // DN Penjualan (Keluar)
    public function indexKeluar()
    {
        $deliveryNotes = DeliveryNote::where('type', 'keluar')
            ->with('details.orderDetail.barang', 'order.customer')
            ->latest()->get();

        return view('penjualan.delivery_note.index', compact('deliveryNotes'));
    }

    // ===========================
    // CREATE
    // ===========================

    // Form DN Pembelian
    public function createMasuk()
    {
        $orders = Orders::where('type', 'purchase')
            ->where('status', '!=', 'draft')
            ->with('details.barang', 'supplier')
            ->get();

        return view('pembelian.delivery_note.create', compact('orders'));
    }

    // Form DN Penjualan
    public function createKeluar()
    {
        $orders = Orders::where('type', 'sales')
            ->where('status', '!=', 'draft')
            ->with('details.barang', 'customer')
            ->get();

        return view('penjualan.delivery_note.create', compact('orders'));
    }

    // ===========================
    // STORE
    // ===========================

    public function storeMasuk(Request $request, $type)
    {
        $request->validate([
            'no' => 'required|unique:delivery_notes,no',
            'tgl' => 'required|date',
            'alamat_kirim' => 'required',
            'order_id' => 'nullable|exists:orders,id',
            'details.*.order_detail_id' => 'required|exists:order_details,id',
            'details.*.keterangan' => 'nullable|string'
        ]);

        $deliveryNote = DeliveryNote::create([
            'no' => $request->no,
            'type' => $type, // masuk/keluar
            'tgl' => $request->tgl,
            'order_id' => $request->order_id,
            'keterangan' => $request->keterangan,
            'alamat_kirim' => $request->alamat_kirim
        ]);

        foreach ($request->details as $item) {
            $detail = DeliveryNoteDetail::create([
                'delivery_note_id' => $deliveryNote->id,
                'order_detail_id' => $item['order_detail_id'],
                'keterangan' => $item['keterangan'] ?? null
            ]);

            // Update stok otomatis
            $barang = $detail->orderDetail->barang;
            if ($type == 'masuk') {
                $barang->stok += $detail->orderDetail->qty;
            } else {
                $barang->stok -= $detail->orderDetail->qty;
            }
            $barang->save();
        }

        $route = $type == 'masuk' ? 'pembelian.delivery-note.index' : 'penjualan.delivery-note.index';
        return redirect()->route($route)->with('success', 'Delivery Note berhasil dibuat.');
    }

    public function store(Request $request, $type)
    {
        $request->validate([
            'no' => 'required|unique:delivery_notes,no',
            'tgl' => 'required|date',
            'alamat_kirim' => 'required',
            'order_id' => 'nullable|exists:orders,id',
            'details.*.order_detail_id' => 'required|exists:order_details,id',
            'details.*.keterangan' => 'nullable|string'
        ]);

        $deliveryNote = DeliveryNote::create([
            'no' => $request->no,
            'type' => $type, // masuk/keluar
            'tgl' => $request->tgl,
            'order_id' => $request->order_id,
            'keterangan' => $request->keterangan,
            'alamat_kirim' => $request->alamat_kirim
        ]);

        foreach ($request->details as $item) {
            $detail = DeliveryNoteDetail::create([
                'delivery_note_id' => $deliveryNote->id,
                'order_detail_id' => $item['order_detail_id'],
                'keterangan' => $item['keterangan'] ?? null
            ]);

            // Update stok otomatis
            $barang = $detail->orderDetail->barang;
            if ($type == 'masuk') {
                $barang->stok += $detail->orderDetail->qty;
            } else {
                $barang->stok -= $detail->orderDetail->qty;
            }
            $barang->save();
        }

        $route = $type == 'masuk' ? 'pembelian.delivery-note.index' : 'penjualan.delivery-note.index';
        return redirect()->route($route)->with('success', 'Delivery Note berhasil dibuat.');
    }

    // ===========================
    // EDIT
    // ===========================

    public function edit(DeliveryNote $deliveryNote)
    {
        if ($deliveryNote->type == 'masuk') {
            $orders = Orders::where('type', 'purchase')
                ->where('status', '!=', 'draft')
                ->with('details.barang', 'supplier')
                ->get();

            $deliveryNote->load('details.orderDetail.barang');

            return view('pembelian.delivery_note.edit', compact('deliveryNote', 'orders'));
        } else {
            $orders = Orders::where('type', 'sales')
                ->where('status', '!=', 'draft')
                ->with('details.barang', 'customer')
                ->get();

            $deliveryNote->load('details.orderDetail.barang');

            return view('penjualan.delivery_note.edit', compact('deliveryNote', 'orders'));
        }
    }

    // ===========================
    // SHOW
    // ===========================

    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load('details.orderDetail.barang', 'order');

        if ($deliveryNote->type == 'masuk') {
            return view('pembelian.delivery_note.show', compact('deliveryNote'));
        } else {
            return view('penjualan.delivery_note.show', compact('deliveryNote'));
        }
    }

    // ===========================
    // UPDATE
    // ===========================

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
        $request->validate([
            'tgl' => 'required|date',
            'details.*.order_detail_id' => 'required|exists:order_details,id',
        ]);

        // Rollback stok lama
        foreach ($deliveryNote->details as $d) {
            $barang = $d->orderDetail->barang;
            if ($deliveryNote->type == 'masuk') {
                $barang->stok -= $d->orderDetail->qty;
            } else {
                $barang->stok += $d->orderDetail->qty;
            }
            $barang->save();
        }

        // Update DN
        $deliveryNote->update([
            'tgl' => $request->tgl,
            'keterangan' => $request->keterangan,
            'alamat_kirim' => $request->alamat_kirim
        ]);

        // Hapus detail lama
        $deliveryNote->details()->delete();

        // Tambah detail baru & update stok
        foreach ($request->details as $item) {
            $detail = DeliveryNoteDetail::create([
                'delivery_note_id' => $deliveryNote->id,
                'order_detail_id' => $item['order_detail_id'],
                'keterangan' => $item['keterangan'] ?? null
            ]);

            $barang = $detail->orderDetail->barang;
            if ($deliveryNote->type == 'masuk') {
                $barang->stok += $detail->orderDetail->qty;
            } else {
                $barang->stok -= $detail->orderDetail->qty;
            }
            $barang->save();
        }

        $route = $deliveryNote->type == 'masuk' ? 'pembelian.delivery-note.index' : 'penjualan.delivery-note.index';
        return redirect()->route($route)->with('success', 'Delivery Note berhasil diupdate.');
    }

    // ===========================
    // DELETE
    // ===========================

    public function destroy(DeliveryNote $deliveryNote)
    {
        // Rollback stok sebelum hapus
        foreach ($deliveryNote->details as $d) {
            $barang = $d->orderDetail->barang;
            if ($deliveryNote->type == 'masuk') {
                $barang->stok -= $d->orderDetail->qty;
            } else {
                $barang->stok += $d->orderDetail->qty;
            }
            $barang->save();
        }

        $deliveryNote->delete();

        $route = $deliveryNote->type == 'masuk' ? 'pembelian.delivery-note.index' : 'penjualan.delivery-note.index';
        return redirect()->route($route)->with('success', 'Delivery Note berhasil dihapus.');
    }

    public function getDeliveryNoteDetail($id)
    {
        $dn = DeliveryNote::with('details.orderDetail.barang')->findOrFail($id);

        $items = $dn->details->map(function ($detail) {
            return [
                'barang_id' => $detail->orderDetail->barang->id,
                'nama_barang' => $detail->orderDetail->barang->nama_barang,
                'qty' => $detail->orderDetail->qty,
            ];
        });

        return response()->json($items);
    }

    // ===========================
    // DETAILS
    // ===========================
    public function showDetailPO($id)
    {
        $dn = DeliveryNote::with([
            'order.supplier',
            'details.orderDetail.barang'
        ])->findOrFail($id);

        return view('pembelian.delivery_note.detail', compact('dn'));
    }

    public function showDetailSO($id)
    {
        $dn = DeliveryNote::with([
            'order.supplier',
            'details.orderDetail.barang'
        ])->findOrFail($id);

        return view('penjualan.surat-jalan.detail', compact('dn'));
    }

}
