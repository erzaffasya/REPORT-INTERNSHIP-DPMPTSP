<?php

use App\Http\Controllers\AksesProgramController;
use App\Http\Controllers\AksesDivisiController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\TalentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivAnggotaController;
use App\Http\Controllers\BeritaAcaraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NilaiUsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/






Route::get('laporan', [LaporanController::class, 'index'])->name('indexLaporan');
Route::get('view-laporan/{id}', [LaporanController::class, 'show'])->name('showLaporan');
Route::put('/updateLaporan/{id}', [LaporanController::class, 'update'])->name('updateLaporan');
// Route::match(['get', 'post'], '/updateLaporan/{id}', [LaporanController::class, 'update']);
Route::get('/Program/{program}/Divisi/{id}', [DivisiController::class, 'show'])->name('showDataDosen');
Route::put('/updateDataDosen/{id}', [DivisiController::class, 'updateDataDosen'])->name('updateDataDosen');
Route::delete('/deleteDataDosen/{id}', [DivisiController::class, 'deleteDataDosen'])->name('deleteDataDosen');

Route::resource('Program', ProgramController::class);
Route::resource('Divisi', DivisiController::class)->except('destroy');
Route::controller(AksesProgramController::class)->group(function () {
    Route::get('aksesProgram', 'index');
    Route::get('tambahAksesProgram/{id}', 'create');
    Route::post('storeAksesProgram', 'store');
    Route::put('aksesProgram/{id}', 'update');
    Route::get('destroyAksesProgram/{id}', 'delete');
});
Route::controller(AksesDivisiController::class)->group(function () {
    Route::get('aksesDivisi', 'index');
    Route::get('tambahAksesDivisi/{id}', 'create');
    Route::post('storeAksesDivisi', 'store');
    Route::put('updateAksesDivisi/{id}', 'update');
    Route::get('destroyAksesDivisi/{id}', 'delete');
});
// Route::resource('aksesDivisi', AksesDivisiController::class);

Route::middleware(['auth', 'role:admin'])->group(function () {
});

Route::group(['middleware' => 'auth'], function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('talent', TalentController::class);
    Route::get('/rekomendasi/{id}/program/{program}', [RecommendationController::class, 'recommend'])->name('rekomendasi-divisi');



    Route::get('/laporan-manual', [ProgramController::class, 'laporanManual'])->name('laporanManual');
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    Route::get('Divisi/{divisi}/Laporan', [LaporanController::class, 'index'])->name('indexLaporan');
    Route::get('view-laporan/{id}', [LaporanController::class, 'show'])->name('showLaporan');
    Route::put('/updateLaporan/{id}', [LaporanController::class, 'update'])->name('updateLaporan');

    Route::put('/verif-laporan/{id}', [LaporanController::class, 'veriflaporan'])->name('veriflaporan');

    Route::get('/Program/{program}/Divisi/{id}', [DivisiController::class, 'show'])->name('showDataDosen');
    Route::put('/updateDataDosen/{id}', [DivisiController::class, 'updateDataDosen'])->name('updateDataDosen');
    Route::delete('/deleteDataDosen/{id}', [DivisiController::class, 'deleteDataDosen'])->name('deleteDataDosen');

    Route::resource('Program', ProgramController::class)->except('destroy');
    Route::get('destroyProgram/{id}', [ProgramController::class, 'destroy']);
    Route::resource('Divisi', DivisiController::class)->except('destroy');
    Route::get('destroyDivisi/{id}', [DivisiController::class, 'destroy']);
    Route::controller(AksesProgramController::class)->group(function () {
        Route::get('aksesProgram', 'index');
        Route::get('tambahAksesProgram/{id}', 'create');
        Route::post('storeAksesProgram', 'store');
        Route::put('aksesProgram/{id}', 'update');
        Route::get('destroyAksesProgram/{id}', 'delete');
    });
    Route::controller(UserController::class)->group(function () {
        Route::get('user', 'index');
        Route::get('user/{id}', 'edit');
        Route::post('storeUser', 'store')->name('storeUser');
        Route::put('updateUser/{id}', 'update')->name('updateUser');
        Route::delete('deleteUser/{id}', 'delete')->name('deleteUser');

        Route::post('addTalentUser/{id}', 'addTalentUser')->name('addTalentUser');
        Route::post('talent-user', 'updateTalent')->name('updateTalentUser');
    });
    Route::controller(AksesDivisiController::class)->group(function () {
        Route::get('aksesDivisi', 'index');
        Route::get('tambahAksesDivisi/{id}', 'create');
        Route::post('storeAksesDivisi', 'store');
        Route::put('updateAksesDivisi/{id}', 'update');
        Route::get('destroyAksesDivisi/{id}', 'delete');
    });
    Route::get('/rekomendasi', function () {
        return view('admin.anggota.rekomendasi');
    });
    Route::get('/cetaknilai', function () {
        return view('admin.anggota.cetaknilai');
    });
    Route::get('/masternilai', function () {
        return view('admin.anggota.masternilai');
    });
    // Route::get('/penilaian', function () {
    //     return view('admin.anggota.penilaian');
    // });

    Route::get('/penilaian/{id}/{divisi}', [NilaiUsersController::class, 'index'])->name('penilaian');
    Route::get('/cetak-nilai/{id}/{divisi}/{program}', [NilaiUsersController::class, 'cetakNilai'])->name('cetak-nilai');
    Route::post('/penilaian', [NilaiUsersController::class, 'store'])->name('penilaian.store');


    Route::get('/berita-acara/{id}', [BeritaAcaraController::class, 'index'])->name('berita.index');
    Route::post('/berita-acara/{id}', [BeritaAcaraController::class, 'store'])->name('berita.store');
    Route::put('/berita-acara/{id}', [BeritaAcaraController::class, 'update'])->name('berita.update');
    Route::delete('/berita-acara/{id}', [BeritaAcaraController::class, 'destroy'])->name('berita.delete');
});


Route::get('/Eksternal/{slug}', [GuestController::class, 'index'])->name('');
Route::get('/guest/Program/{program}/Divisi/{id}', [GuestController::class, 'show'])->name('');
Route::get('/Lihat-laporan', [GuestController::class, 'lihatlaporan'])->name('guestlihatlaporan');
Route::get('/Lihat-laporan/Data', [GuestController::class, 'carilaporan'])->name('filterlaporan');
Route::get('/Detail-laporan/{id}', [GuestController::class, 'detaillaporan'])->name('guestdetaillaporan');


require __DIR__ . '/auth.php';
