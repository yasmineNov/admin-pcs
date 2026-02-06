<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::all();
        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_bank' => 'required|unique:banks',
            'nama_bank' => 'required',
        ]);

        Bank::create($request->all());

        return redirect()->route('banks.index')
            ->with('success','Bank berhasil ditambahkan');
    }

    public function edit(Bank $bank)
    {
        return view('banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'kode_bank' => 'required|unique:banks,kode_bank,' . $bank->id,
            'nama_bank' => 'required',
        ]);

        $bank->update($request->all());

        return redirect()->route('banks.index')
            ->with('success','Bank berhasil diupdate');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->route('banks.index')
            ->with('success','Bank berhasil dihapus');
    }
}
