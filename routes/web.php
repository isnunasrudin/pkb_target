<?php

use App\Http\Controllers\CalonDewanController;
use App\Http\Controllers\VoterController;
use App\Models\CalonDewan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::redirect('home', 'dapil')->name('home');

Route::get('/profile', 'ProfileController@index')->name('profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::resource('voters', VoterController::class);

Route::get('dapil', 'SuaraController@showDapil')->name('dapil');

Route::get('dapil/{dapil}', 'SuaraController@dapil');
Route::get('kecamatan/{kecamatan}', 'SuaraController@desa')->name('desa');
Route::get('desa/{address}', 'SuaraController@tps')->name('tps');
Route::get('rt/{address}', 'SuaraController@rt')->name('rt');

// Route::get('/test', 'TestController')->name('test');
// Route::get('/test2', 'TestController@test2')->name('test');
Route::post('/sebar', 'SuaraController@hitung_sebaran')->name('sebar');
Route::delete('/sebar', 'SuaraController@reset_sebaran')->name('reset_sebaran');
Route::get('/export', 'ExportController@export')->name('export');

Route::resource('calon_dewan', CalonDewanController::class)->withTrashed(['destroy']);

Route::get('test', function () {
    CalonDewan::orderBy('id')->get()->groupBy('dapil')->each(function ($calonDewans) {
        $order = 0;
        $calonDewans->each(function ($calonDewan) use (&$order) {
            $calonDewan->update([
                'order' => $order,
            ]);
            $order++;
        });
    });
});

Route::get('recap', 'RecapController@index')->name('recap');
Route::get('recap/dpt', 'RecapController@dpt')->name('recap.dpt');
