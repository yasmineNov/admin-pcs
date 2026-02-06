<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kas;

class KasController extends Controller
{
    public function index()
    {
        $data = Kas::orderBy('tanggal')->orderBy('id')->get();
        return view('kas.index', compact('data'));
    }

    public function create()
    {
        return view('kas.create');
    }

    public function store(Request $request)
    {
        $saldoTerakhir = Kas::latest('id')->value('saldo') ?? 0;

        $saldoBaru = $saldoTerakhir 
                    + $request->debit 
                    - $request->kredit;

        Kas::create([
            'tanggal'      => $request->tanggal,
            'no_transaksi' => $request->no_transaksi,
            'keterangan'   => $request->keterangan,
            'debit'        => $request->debit ?? 0,
            'kredit'       => $request->kredit ?? 0,
            'saldo'        => $saldoBaru,
            'jenis'        => $request->jenis,
        ]);

        return redirect()->route('kas.index');
    }
}
