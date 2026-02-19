<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\DeliveryNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    //     private function generateNomor($type)
// {
//     $bulan = now()->month;
//     $tahun = now()->year;

    //     $romawi = [
//         1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
//         5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
//         9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
//     ];

    //     $kode = $type == 'keluar' ? 'PCS-SO' : 'PCS-PO';

    //     $count = Orders::where('type', $type)
//         ->whereMonth('created_at', $bulan)
//         ->whereYear('created_at', $tahun)
//         ->count() + 1;

    //     $noUrut = str_pad($count, 3, '0', STR_PAD_LEFT);

    //     return $noUrut . '/' . $kode . '/' . $romawi[$bulan] . '/' . $tahun;
// }

    public function index()
    {
        return Orders::with(['customers', 'supplier'])
            ->latest()
            ->get();
    }
    //============================= pembelian ========================================================
    // Tampilkan semua PO
    public function indexPO()
    {
        $orders = Orders::with(['supplier', 'details.barang'])
            ->where('type', 'purchase')
            ->latest()
            ->get();

        return view('pembelian.purchase-order.index', compact('orders'));
    }


    public function createPO()
    {
        $suppliers = Supplier::all();
        $barangs = Barang::all();
        $deliveryNotes = DeliveryNote::where('type', 'masuk')->get(); // <- ini
        return view('pembelian.purchase-order.create', compact('suppliers', 'barangs','deliveryNotes'));
    }

    public function storePO(Request $request)
    {
        $request->validate([
            'tgl' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'barang_id.*' => 'required|exists:barangs,id',
            'qty.*' => 'required|numeric|min:0.01',
            'harga.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Hitung subtotal tiap barang
            $subtotals = [];
            foreach ($request->barang_id as $i => $barangId) {
                $subtotals[$i] = $request->qty[$i] * $request->harga[$i];
            }

            $dpp = array_sum($subtotals);
            $pajak = $dpp * 0.11; // PPN 11%
            $total = $dpp + $pajak;

            // Simpan ke tabel orders
            $order = Orders::create([
                'no' => $request->no,
                'type' => 'purchase',
                'tgl' => $request->tgl,
                'supplier_id' => $request->supplier_id,
                'customer_id' => null,
                'pajak' => $pajak,
                'dpp' => $dpp,
                'total' => $total,
                'status' => 'Belum Lunas',
                'keterangan' => 'TOP: ' . $request->top . ' | Tgl Kirim: ' . $request->tgl_kirim . ' | ' . $request->keterangan,
            ]);

            // Simpan order_details dan update stok
            foreach ($request->barang_id as $i => $barangId) {
                \App\Models\OrderDetail::create([
                    'order_id' => $order->id,
                    'barang_id' => $barangId,
                    'harga' => $request->harga[$i],
                    'qty' => $request->qty[$i],
                    'hpp' => null,
                    'subtotal' => $subtotals[$i],
                    'keterangan' => null,
                    'qty_sent' => 0,
                ]);

                // Tambah stok karena PO masuk
                $barang = \App\Models\Barang::find($barangId);
                $barang->stok += $request->qty[$i];
                $barang->save();
            }

            DB::commit();

            return redirect()->route('pembelian.purchase-order.index')
                ->with('success', 'Purchase Order berhasil dibuat dan stok diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $po = Orders::with('details.barang')->findOrFail($id);

        return response()->json($po->details);
    }

    public function showDetailPO($id)
    {
        $po = Orders::with('details.barang')->findOrFail($id);
        return view('pembelian.purchase-order.detail', compact('po'));
    }

    //============================= End pembelian ========================================================

    //============================= PENJUALAN ========================================================
    // Tampilkan semua PO
    public function indexSO()
    {
        $orders = Orders::with(['customer', 'details.barang'])
            ->where('type', 'sales')
            ->latest()
            ->get();

        return view('penjualan.sales-order.index', compact('orders'));
    }


    public function createSO()
    {
        $suppliers = Supplier::all();
        $customer = Customer::all();
        $barangs = Barang::all();
        return view('penjualan.sales-order.create', compact('customer', 'barangs'));
    }

    public function storeSO(Request $request)
    {
        $request->validate([
            'tgl' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'barang_id.*' => 'required|exists:barangs,id',
            'qty.*' => 'required|numeric|min:0.01',
            'harga.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Hitung subtotal tiap barang
            $subtotals = [];
            foreach ($request->barang_id as $i => $barangId) {
                $subtotals[$i] = $request->qty[$i] * $request->harga[$i];
            }

            $dpp = array_sum($subtotals);
            $pajak = $dpp * 0.11; // PPN 11%
            $total = $dpp + $pajak;

            // Simpan ke tabel orders
            $order = Orders::create([
                'no' => $request->no,
                'type' => 'sales',
                'tgl' => $request->tgl,
                'supplier_id' => null,
                'customer_id' => $request->customer_id,
                'pajak' => $pajak,
                'dpp' => $dpp,
                'total' => $total,
                'status' => 'Belum Lunas',
                'keterangan' => 'TOP: ' . $request->top . ' | Tgl Kirim: ' . $request->tgl_kirim . ' | ' . $request->keterangan,
            ]);

            // Simpan order_details dan update stok
            foreach ($request->barang_id as $i => $barangId) {
                \App\Models\OrderDetail::create([
                    'order_id' => $order->id,
                    'barang_id' => $barangId,
                    'harga' => $request->harga[$i],
                    'qty' => $request->qty[$i],
                    'hpp' => null,
                    'subtotal' => $subtotals[$i],
                    'keterangan' => null,
                    'qty_sent' => 0,
                ]);

                // Tambah stok karena PO masuk
                $barang = \App\Models\Barang::find($barangId);
                $barang->stok += $request->qty[$i];
                $barang->save();
            }

            DB::commit();

            return redirect()->route('penjualan.sales-order.index')
                ->with('success', 'Sales Order berhasil dibuat dan stok diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function detailSO($id)
    {
        $so = Orders::with('details.barang')->findOrFail($id);

        return response()->json($so->details);
    }

    public function showDetailSO($id)
    {
        $po = Orders::with('details.barang')->findOrFail($id);
        return view('penjualan.sales-order.detail', compact('po'));
    }


    //============================= End PENJUALAN ========================================================
}