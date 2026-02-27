<?php

use App\Models\Icd10;
use App\Models\Barang;
use App\Models\Pasien;
use App\Models\Registrasi;
use Illuminate\Http\Request;
use App\Models\TarifTindakan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within the "web" middleware group.
|
*/

if (!function_exists('routeName')) {
    function routeName($row, $url)
    {
        if (!empty($row['method'])) {
            foreach ($row['method'] as $method) {
                if (class_exists("\\App\\Livewire\\" . ucfirst($url) . "\\" . $method)) {
                    Route::get(
                        '/' . ($method == "Index" ? "" : strtolower($method) . "/{data?}"),
                        "\\App\\Livewire\\" . ucfirst($url)  . "\\" .  ucfirst($method)
                    )
                        ->middleware(['role_or_permission:administrator|' . str_replace('\\', '', strtolower($url))])
                        ->name(str_replace('\\', '.', strtolower($url)) . '.' . strtolower($method));
                }
            }
        }
    }
}

if (!function_exists('subRoutes')) {
    function subRoutes($subRoutes, $parentUrl)
    {
        foreach ($subRoutes as $row) {
            $url = str_replace([' ', '/', '&', '\'', ',', '(', ')', '.'], '', strtolower($row['title']));
            Route::prefix($url)->group(function () use ($parentUrl, $url, $row) {
                if (empty($row['sub_menu'])) {
                    routeName($row, ucfirst($parentUrl) . "\\" . ucfirst($url));
                } else {
                    subRoutes($row['sub_menu'], ucfirst($parentUrl) . "\\" . ucfirst($url));
                }
            });
        }
    }
}

Route::middleware(['auth'])->group(function () {
    Route::post('logout', function () {
        auth()->logout();
        return  redirect('login');
    });
    Route::redirect('/', '/home');
    Route::get('/home', \App\Livewire\Home::class)->name('home');
    Route::get('/gantipassword', \App\Livewire\Gantipassword::class);

    Route::prefix('cari')->group(function () {
        Route::get('pasien', function (Request $req) {
            return Pasien::where(
                fn($q) => $q
                    ->where('nik', 'like', "%$req->cari%")
                    ->orWhere('id', 'like', "%$req->cari%")->orWhere('alamat', 'like', "%$req->cari%")
                    ->orWhere('nama', 'like', "%$req->cari%")
                    ->orWhereHas('registrasi', fn($q) => $q->where('id', 'like', "%$req->cari%"))
            )->orderBy('nama', 'asc')->get()
                ->map(fn($q) => [
                    'id' => $q->id,
                    'text' => $q->id . ' - ' . $q->nama . ', ' . $q->alamat,
                    'rm' => $q->id,
                    'nik' => $q->nik ?: '',
                    'nama' => $q->nama,
                    'alamat' => $q->alamat,
                ])->toArray();
        });
        Route::get('member', function (Request $req) {
            return Pasien::whereHas('member')->where(
                fn($q) => $q
                    ->where('nik', 'like', "%$req->cari%")
                    ->orWhere('id', 'like', "%$req->cari%")->orWhere('alamat', 'like', "%$req->cari%")
                    ->orWhere('nama', 'like', "%$req->cari%")
                    ->orWhereHas('registrasi', fn($q) => $q->where('id', 'like', "%$req->cari%"))
            )->orderBy('nama', 'asc')->get()
                ->map(fn($q) => [
                    'id' => $q->id,
                    'text' => $q->id . ' - ' . $q->nama . ', ' . $q->alamat,
                    'rm' => $q->id,
                    'nik' => $q->nik ?: '',
                    'nama' => $q->nama,
                    'alamat' => $q->alamat,
                ])->toArray();
        });
        Route::get('barang', function (Request $req) {
            return Barang::where('nama', 'like', "%$req->cari%")->where('persediaan', 'Apotek')->get()->map(fn($q) => [
                'id' => $q->id,
                'text' => $q->nama,
            ])->toArray();
        });
        Route::get('tariftindakan', function (Request $req) {
            return TarifTindakan::where('nama', 'like', "%$req->cari%")->get()->map(fn($q) => [
                'id' => $q->id,
                'text' => $q->nama,
            ])->toArray();
        });
        Route::get('registrasi', function (Request $req) {
            return Registrasi::whereHas('pasien', function ($query) use ($req) {
                $query->where(
                    fn($q) => $q
                        ->where('nik', 'like', "%$req->cari%")
                        ->orWhere('id', 'like', "%$req->cari%")->orWhere('alamat', 'like', "%$req->cari%")
                        ->orWhere('nama', 'like', "%$req->cari%")
                );
            })->get()->map(fn($q) => [
                'id' => $q->id,
                'text' => $q->id . ' - ' . $q->pasien->nama . ', ' . $q->pasien->alamat,
                'rm' => $q->pasien->id,
                'nik' => $q->pasien->nik ?: '',
                'nama' => $q->pasien->nama,
                'alamat' => $q->pasien->alamat,
            ])->toArray();
        });
    });

    foreach (collect(config('sidebar.menu'))->sortBy('title')->toArray() as $row) {
        $url = str_replace([' ', '/', '&', '\'', ',', '(', ')', '.'], '', strtolower($row['title']));
        Route::prefix($url)->group(function () use ($url, $row) {
            if (empty($row['sub_menu'])) {
                routeName($row, $url);
            } else {
                subRoutes($row['sub_menu'], $url);
            }
        });
    }
});
