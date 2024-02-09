<?php

use App\Http\Controllers\backend\CoursedateController;
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

/*
Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/', 'App\Http\Controllers\HomeController@index');
Route::get('/Startseite', 'App\Http\Controllers\HomeController@index');
Route::get('/Impressum', 'App\Http\Controllers\ImpressumController@getImpressumDaten');
Route::get('/Information/Datenschutzerklaerung', 'App\Http\Controllers\DatenschutzerklärungController@getDatenschutzerklärungDaten');

Route::get('/Angebot', 'App\Http\Controllers\HomeController@offer');
Route::get('/Sportart', 'App\Http\Controllers\HomeController@sportType');
Route::get('/Trainer', 'App\Http\Controllers\HomeController@trainer');
Route::get('/Sportgeräte', 'App\Http\Controllers\HomeController@sportUnit');
Route::get('/Kurse', 'App\Http\Controllers\HomeController@course');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    /*  ToDO: Auf Resource umstellen
        Route::resource('/post', CoursedateController::clas;
    */
        Route::get('/backend/CourseDate', 'App\Http\Controllers\backend\CoursedateController@index')->name('backend.courseDate.index');
        Route::get('/backend/CourseDateCreate', 'App\Http\Controllers\backend\CoursedateController@create')->name('backend.courseDate.create');
        Route::get('/backend/CourseDateEdit/{coursedate}', 'App\Http\Controllers\backend\CoursedateController@edit')->name('backend.courseDate.edit');
        Route::put('/backend/CourseDateUpdate/{coursedate}', 'App\Http\Controllers\backend\CoursedateController@update')->name('backend.courseDate.update');
        Route::put('/backend/CourseDateStore', 'App\Http\Controllers\backend\CoursedateController@store')->name('backend.courseDate.store');
        Route::get('/backend/CourseDateDestroy/{coursedate}', 'App\Http\Controllers\backend\CoursedateController@destroy')->name('backend.courseDate.destroy');
});

Route::get('/Kursemanager', 'App\Http\Controllers\CoursedateController@index');
