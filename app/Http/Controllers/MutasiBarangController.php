<?php

namespace App\Http\Controllers;

use App\Models\MutasiBarang;
use App\Models\Barang;
use Illuminate\Http\Request;

class MutasiBarangController extends Controller
{

    public function index(Request $request)
    {
        $barangId = $request->barang_id;
        $barangs = Barang::orderBy('nama_barang')->get();
        $mutasis = collect();

        if ($barangId) {
            $saldo = 0;

            $mutasis = MutasiBarang::where('barang_id', $barangId)
                ->orderBy('tgl_mutasi')
                ->orderBy('id')
                ->get()
                ->map(function ($mutasi) use (&$saldo) {

                    if ($mutasi->tipe === 'IN') {
                        $saldo += $mutasi->qty;
                        $mutasi->masuk = $mutasi->qty;
                        $mutasi->keluar = 0;
                    } elseif ($mutasi->tipe === 'OUT') {
                        $saldo -= $mutasi->qty;
                        $mutasi->masuk = 0;
                        $mutasi->keluar = $mutasi->qty;
                    }

                    $mutasi->saldo = $saldo;
                    return $mutasi;
                });
        }

        return view('mutasi_barangs.index', compact('mutasis', 'barangs', 'barangId'));
    }

    public function create()
    {
        $barangs = Barang::orderBy('nama_barang')->get();
        return view('mutasi_barangs.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_mutasi' => 'required|date',
            'barang_id'  => 'required|exists:barangs,id',
            'qty'        => 'required|integer|min:1',
            'tipe'       => 'required|in:IN,OUT',
        ]);

        $mutasi = MutasiBarang::create([
            'tgl_mutasi' => $request->tgl_mutasi,
            'barang_id'  => $request->barang_id,
            'qty'        => $request->qty,
            'tipe'       => $request->tipe,
            'keterangan' => $request->keterangan ?? null,
        ]);

        if ($mutasi->tipe === 'IN') {
            $mutasi->barang->increment('stok', $mutasi->qty);
        } else {
            $mutasi->barang->decrement('stok', $mutasi->qty);
        }

        return redirect()->route('mutasi-barangs.index')
            ->with('success', 'Mutasi barang berhasil disimpan');
    }

    public function destroy(MutasiBarang $mutasiBarang)
    {
        if ($mutasiBarang->tipe === 'IN') {
            $mutasiBarang->barang->decrement('stok', $mutasiBarang->qty);
        } else {
            $mutasiBarang->barang->increment('stok', $mutasiBarang->qty);
        }

        $mutasiBarang->delete();

        return redirect()->route('mutasi-barangs.index')
            ->with('success', 'Mutasi barang berhasil dihapus');
    }
}

