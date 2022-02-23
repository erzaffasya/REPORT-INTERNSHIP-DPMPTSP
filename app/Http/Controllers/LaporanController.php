<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index($divisi)
    {
        $laporan = Laporan::where('user_id', Auth::user()->id)->where('divisi_id', $divisi)->orderBy('id', 'DESC')->get();
        $divisi = Divisi::find($divisi);
        return view ('magang.laporan.index', compact('laporan','divisi'));
    }

    public function create()
    {
        // $kategori = Kategori::all();
        return view('admin.produk.tambah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required',
            'detail' => 'required',
            'stok' => 'required',
            'gambar1' => 'required', 
        ]);

        $date = date("his");
        $extension = $request->file('gambar1')->extension();
        $file_name = "Produk_$date.$extension";
        $path = $request->file('gambar1')->storeAs('public/Produk', $file_name);

        Produk::create([
            'nama' => $request->nama,
            'detail' => $request->detail,
            'gambar' => $file_name,
            'harga' => $request->harga,
            'stok' => $request->stok,
        ]);
        return redirect()->route('produk.index')
            ->with('success', 'Produk Berhasil Ditambahkan');
    }
    public function show($id)
    {
        // $minggu = Laporan::findorFail($minggu);
        $laporan = Laporan::find($id);
        return view ('magang.laporan.view', compact('laporan'));
    }


    public function edit($id)
    {
        $produk = Produk::find($id);
        // $kategori = Kategori::all();
        return view('admin.produk.edit',compact('produk'));
    }

    public function update(Request $request)
    {
        $Laporan = Laporan::findOrFail(Auth::user()->id);

        if($request->senin != NULL){
            $Laporan->senin = $request->senin;
        }
        if($request->selasa != NULL){
            $Laporan->selasa = $request->selasa;
        }
        if($request->rabu != NULL){
            $Laporan->rabu = $request->rabu;
        }
        if($request->kamis != NULL){
            $Laporan->kamis = $request->kamis;
        }
        if($request->jumat != NULL){
            $Laporan->jumat = $request->jumat;
        }
        if($request->mingguan != NULL){
            $Laporan->mingguan = $request->mingguan;
        }
        $Laporan->save();

        return redirect()->back()
        ->with('edit', 'Laporan Berhasil Dibuat');
    }

    public function destroy($id)
    {
        $Produk = Produk::findOrFail($id);
        Storage::delete("public/Produk/$Produk->gambar");
        $Produk->delete();
        return redirect()->route('produk.index')
            ->with('delete', 'Produk Berhasil Dihapus');
    }

    public function grid()
    {
        $produk = Produk::all();
        // $kategori = Kategori::all();
        return view('admin.produk.grid', compact('produk'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
}
