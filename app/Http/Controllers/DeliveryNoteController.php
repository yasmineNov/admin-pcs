<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\DeliveryNoteDetail;
use App\Models\MutasiBarang;
use App\Models\Orders;
use App\Models\Customer;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    public function index()
    {
        $data = DeliveryNote::with('order.customer')
                ->orderBy('tgl', 'desc')
                ->get();

        return view('penjualan.surat-jalan.index', compact('data'));
    }

    public function create()
    {
        $customers = Customer::all();
        $barangs = Barang::all();
        $orders = Orders::with('customer','details.barang')
                    ->where('status','approved')
                    ->get();

        return view('penjualan.surat-jalan.create', compact('customers', 'orders','barangs'));
    }

    public function store(Request $request)
{
    $request->validate([
        'tgl' => 'required|date',
        'customer_id' => 'required|exists:customers,id',
        'barang_id' => 'required|array',
        'qty' => 'required|array',
    ]);

    DB::transaction(function () use ($request) {

        // $deliveryNote = DeliveryNote::create([
        //     'no' => 'SJ-' . now()->format('YmdHis'),
        //     'tgl' => $request->tgl_sj,
        //     'keterangan' => null,
        //     'alamat_kirim' => null,
        //     'order_id' => null, // karena manual
        // ]);

        $deliveryNote = DeliveryNote::create([
    'no' => 'SJ-' . now()->format('YmdHis'),
    'tgl' => $request->tgl,
    'keterangan' => null,
    'alamat_kirim' => $request->alamat_kirim,
    'order_id' => $request->order_id,
]);



        foreach ($request->barang_id as $index => $barangId) {

            $qtyKirim = $request->qty[$index];
            $barang = \App\Models\Barang::findOrFail($barangId);

            if ($qtyKirim <= 0) continue;

            if ($barang->stok < $qtyKirim) {
                throw new \Exception("Stok {$barang->nama_barang} tidak cukup!");
            }

            DeliveryNoteDetail::create([
                'delivery_note_id' => $deliveryNote->id,
                'barang_id' => $barang->id,
                'qty_kirim' => $qtyKirim,
                'keterangan' => null,
            ]);

            MutasiBarang::create([
                'tgl_mutasi' => now(),
                'barang_id' => $barang->id,
                'qty' => $qtyKirim,
                'tipe' => 'OUT',
                'keterangan' => 'Surat Jalan ' . $deliveryNote->no,
            ]);

            $barang->decrement('stok', $qtyKirim);
        }
    });

    return redirect()->route('surat-jalan.index')
        ->with('success','Surat Jalan berhasil dibuat');
}


    public function detail($id)
    {
        $sj = DeliveryNote::with('details.orderDetail.barang', 'order.customer')
                ->findOrFail($id);

        return response()->json([
            'no_sj' => $sj->no,
            'customer' => $sj->order->customer->nama_customer ?? '-',
            'details' => $sj->details->map(function ($d) {
                return [
                    'barang' => $d->orderDetail->barang->nama_barang ?? '-',
                    'qty' => $d->orderDetail->qty ?? 0
                ];
            })
        ]);
    }
}
