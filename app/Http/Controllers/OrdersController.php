<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use DB;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index()
    {
        return Orders::with(['customer', 'supplier'])
                    ->latest()
                    ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no' => 'required|unique:orders',
            'type' => 'required|in:sales,purchase',
            'tgl' => 'required|date',
            'details' => 'required|array'
        ]);

        DB::transaction(function () use ($request) {

            $order = Orders::create($request->except('details'));

            foreach ($request->details as $item) {
                $order->details()->create([
                    'barang_id' => $item['barang_id'],
                    'harga' => $item['harga'],
                    'qty' => $item['qty'],
                    'hpp' => $item['hpp'] ?? null,
                    'subtotal' => $item['harga'] * $item['qty'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);
            }

        });

        return response()->json(['message' => 'Order berhasil dibuat']);
    }

    public function show(Orders $order)
    {
        return $order->load('details.barang');
    }

    public function update(Request $request, Orders $order)
    {
        $order->update($request->all());

        return response()->json(['message' => 'Order updated']);
    }

    public function destroy(Orders $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted']);
    }
}