<?php

namespace App\Http\Controllers;

use App\Models\Akses_divisi;
use App\Models\Akses_program;
use App\Models\Divisi;
use App\Models\Laporan;
use App\Models\NilaiUsers;
use App\Models\Program;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function index()
    {
        $Program = Program::all();
        return view('admin.program.index', compact('Program'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        // $kategori = Kategori::all();
        return view('admin.program.tambah');
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'judul' => 'required',
            'detail' => 'required',
            'periode_mulai' => 'required',
            'periode_berakhir' => 'required',
        ]);

        // $date = date("his");
        // $extension = $request->file('gambar1')->extension();
        // $file_name = "Program_$date.$extension";
        // $path = $request->file('gambar1')->storeAs('public/Program', $file_name);
        Program::create([
            'judul' => $request->judul,
            'detail' => $request->detail,
            // 'gambar' => $file_name,
            'periode_mulai' => Carbon::parse($request->periode_mulai),
            'periode_berakhir' => Carbon::parse($request->periode_berakhir),
            'status' => true,
        ]);
        return redirect()->route('Program.index')
            ->with('success', 'Program Berhasil Ditambahkan');
    }
    public function show($id)
    {
        $Akses_program = Akses_program::join('users', 'users.id', 'akses_program.user_id')
            ->where('akses_program.program_id', $id)
            ->where('users.role', 'magang')->get();
        $Divisi = Divisi::where('program_id', $id)->get();
        $user = User::all();
        $Program = Program::where('id', $id)->first();
        $today = Carbon::now();
        $end = Carbon::parse($Program->periode_berakhir);
        $periode = $today->diffInDays($end, false);
        $userDivision = Auth::user()->getDivisionForProgram($Program->id);
        // dd($userDivision);
        return view('admin.program.show', compact('Program', 'Akses_program', 'userDivision', 'Divisi', 'periode', 'user'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    public function edit($id)
    {
        $Program = Program::find($id);
        // $kategori = Kategori::all();
        return view('admin.program.edit', compact('Program'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required',
            'detail' => 'required',
            'periode_mulai' => 'required',
            'periode_berakhir' => 'required',
        ]);

        $Program = Program::findOrFail($id);


        $Program->judul = $request->judul;
        $Program->detail = $request->detail;
        $Program->periode_mulai = $request->periode_mulai;
        $Program->status = true;
        $Program->periode_berakhir = $request->periode_berakhir;
        $Program->save();

        return redirect()->route('Program.index')
            ->with('edit', 'Program Berhasil Diedit');
    }

    public function destroy($id)
    {
        // dd('asd');
        $Program = Program::find($id);
        $Divisi = Divisi::where('program_id', $Program->id)->get();
        // dd($Divisi, $Program);
        foreach ($Divisi as $item) {
            $Nilai = NilaiUsers::where('divisi_id', $item->id)->delete();
        }
        $Divisi = Divisi::where('program_id', $Program->id)->delete();
        $Program->delete();
        // Storage::delete("public/Program/$Program->gambar");
        // $Program->delete();
        return redirect()->route('Program.index')
            ->with('delete', 'Program Berhasil Dihapus');
    }

    public function tambahjadwal()
    {
        Program::create([
            'judul' => '1',
            'detail' => '1',
        ]);
        return True;
    }

    public function laporanManual()
    {
        $program = Program::all();
        $data = NULL;
        foreach ($program as $item) {
            $date_diff = Carbon::parse($item->periode_mulai)->diffInDays(Carbon::parse($item->periode_berakhir), false) + 1;
            if ($date_diff >= "0") {
                $divisi = Divisi::select('akses_divisi.user_id', 'akses_divisi.divisi_id', 'divisi.program_id')->join('akses_divisi', 'akses_divisi.divisi_id', 'divisi.id')->where('divisi.program_id', $item->id)->get();
                foreach ($divisi as $divisis) {
                    Laporan::create([
                        'isVerif' => False,
                        'user_id' => $divisis->user_id,
                        'divisi_id' => $divisis->divisi_id,
                    ]);
                }
            }
        }
        return back();
    }
}
