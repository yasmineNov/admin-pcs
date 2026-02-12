<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\MutasiBarang;


class DeliveryNoteController extends Controller
{
    public function index()
    {
        return DeliveryNote::with('order')
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no' => 'required|unique:delivery_notes',
            'tgl' => 'required|date',
            'order_id' => 'required|exists:orders,id',
            'details' => 'required|array'
        ]);

        DB::transaction(function () use ($request) {

            $dn = DeliveryNote::create(
                $request->only(['no', 'tgl', 'keterangan', 'alamat_kirim', 'order_id'])
            );

            foreach ($request->details as $detail) {

                // ambil order detail + barang
                $orderDetail = \App\Models\OrderDetail::with('barang')
                    ->findOrFail($detail['order_detail_id']);

                $barang = $orderDetail->barang;

                $qtyKirim = $orderDetail->qty;
                // nanti bisa dikembangkan partial kirim pakai qty_sent

                // VALIDASI stok cukup
                if ($barang->stok < $qtyKirim) {
                    throw new \Exception("Stok tidak cukup untuk barang {$barang->nama}");
                }

                // buat delivery note detail
                $dnd = $dn->details()->create([
                    'order_detail_id' => $orderDetail->id,
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);

                // kurangi stok barang
                $barang->decrement('stok', $qtyKirim);

                // catat mutasi barang
                MutasiBarang::create([
                    'barang_id' => $barang->id,
                    'qty' => -$qtyKirim,
                    'delivery_note_detail_id' => $dnd->id,
                    'keterangan' => 'Delivery Note: ' . $dn->no,
                ]);
            }
        });

        return response()->json(['message' => 'Delivery Note created & stok updated']);
    }


    public function show(DeliveryNote $deliveryNote)
    {
        return $deliveryNote->load('details.orderDetail.barang');
    }

    public function destroy(DeliveryNote $deliveryNote)
    {
        $deliveryNote->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
