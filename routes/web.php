<?php

use App\Http\Controllers\Backend\CourseController;
use App\Http\Controllers\Backend\CoursedateController;
use App\Http\Controllers\Backend\OrganiserController;
use App\Http\Controllers\Backend\SportEquipmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseBooking\CourseParticipantController;
use App\Models\Organiser;
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

Route::get('/Angebot', 'App\Http\Controllers\HomeController@offer')->name('frontend.offer');
Route::get('/Sportart', 'App\Http\Controllers\HomeController@sportType');
Route::get('/Trainer', 'App\Http\Controllers\HomeController@trainer');
Route::get('/Sportgeraete', 'App\Http\Controllers\HomeController@sportUnit');
Route::get('/Kurse', 'App\Http\Controllers\HomeController@coursetype');
Route::get('/Kurseangebot/{id}', 'App\Http\Controllers\HomeController@courseDate')->name('frontend.course');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();

        if (!$organiser) {
            $organiser = new Organiser;
            // Setzen Sie hier die Standardwerte für das Organiser-Objekt
        }

        return view('dashboard', ['organiser' => $organiser]);
    })->name('dashboard');
});

Route::get('/Kursbuchung', [CourseParticipantController::class, 'index'])->name('courseBooking.course.index');
Route::get('/Kursbuchung/Participant', [CourseParticipantController::class, 'indexParticipant'])->name('courseBooking.course.indexParticipant');
Route::get('/Kursbuchung/edit/{coursedateId}', [CourseParticipantController::class, 'edit'])->name('courseBooking.course.edit');
Route::put('/Kursbuchung/update/{coursedate}', [CourseParticipantController::class, 'update'])->name('courseBooking.course.update');
Route::get('/Kursbuchung/buchen/{coursedateId}', [CourseParticipantController::class, 'book'])->name('courseBooking.course.book');
Route::get('/Kursbuchung/stornieren/{coursedateId}/{courseBookId}', [CourseParticipantController::class, 'destroyBooked'])->name('courseBooking.course.destroyBooked');

Route::middleware('admin:admin')->group(function () {
    Route::get('admin/login', [AdminController::Class, 'loginForm']);
    Route::post('admin/login', [AdminController::Class, 'store'])->name('admin.login');
});

Route::middleware([
    'auth:sanctum,admin',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/admin/dashboard', function () {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();

        if (!$organiser) {
            $organiser = new Organiser;
            // Setzen Sie hier die Standardwerte für das Organiser-Objekt
        }

        return view('admin/dashboard', ['organiser' => $organiser]);
    })->name('admin.dashboard')->middleware('auth:admin');


    /*  ToDo: Auf Resource umstellen
        Route::resource('/post', CoursedateController::class;
    */
    Route::get('/backend/CourseDate', [CoursedateController::class, 'index'])->name('backend.courseDate.index');
    Route::get('/backend/CourseDateAll', [CoursedateController::class, 'indexAll'])->name('backend.courseDate.indexAll');
    Route::get('/backend/CourseDateCreate', [CoursedateController::class, 'create'])->name('backend.courseDate.create');
    Route::get('/backend/CourseDateEdit/{coursedate}', [CoursedateController::class, 'edit'])->name('backend.courseDate.edit');
    Route::get('/backend/CourseDateEditGebucht/{coursedate}', [CoursedateController::class, 'editBooked'])->name('backend.courseDate.editBooked');
    Route::put('/backend/CourseDateUpdate/{coursedate}', [CoursedateController::class, 'update'])->name('backend.courseDate.update');
    Route::put('/backend/CourseDateUpdateGebucht/{coursedate}', [CoursedateController::class, 'updateBooked'])->name('backend.courseDate.updateBooked');
    Route::put('/backend/CourseDateStore', [CoursedateController::class, 'store'])->name('backend.courseDate.store');
    Route::get('/backend/CourseDateDestroy/{coursedate}', [CoursedateController::class, 'destroy'])->name('backend.courseDate.destroy');
    Route::get('/backend/CourseDatesportingEquipment/{coursedate}', [CoursedateController::class, 'sportingEquipment'])->name('backend.courseDate.sportingEquipment');
    Route::get('/backend/Book/{coursedateId}', [CoursedateController::class, 'Book'])->name('backend.courseDate.Book');
    Route::get('/backend/destroyBooked/{coursedateId}/{courseBookId}', [CoursedateController::class, 'destroyBooked'])->name('backend.courseDate.destroyBooked');
    Route::get('/backend/CourseDateEquipmentBooked/{coursedateId}/{sportequipmentId}', [CoursedateController::class, 'equipmentBooked'])->name('backend.courseDate.equipmentBooked');
    Route::get('/backend/CourseDateEquipmentBookedDestroy/{coursedateId}/{kursId}/{sportgeraet}', [CoursedateController::class, 'equipmentBookedDestroy'])->name('backend.courseDate.equipmentBookedDestroy');
    Route::get('/backend/CourseDateTrainerPick{coursedateId}', [CoursedateController::class, 'trainerRegister'])->name('backend.courseDate.trainerRegister');
    Route::get('/backend/CourseDateTrainerDestroy/{coursedateId}', [CoursedateController::class, 'trainerDestroy'])->name('backend.courseDate.trainerDestroy');

    Route::get('/backend/Sportgeraete', [SportEquipmentController::class, 'index'])->name('backend.sportEquipment.index');
    Route::get('/backend/SportgeraeteAlle', [SportEquipmentController::class, 'indexAll'])->name('backend.sportEquipment.indexAll');
    Route::get('/backend/SportgeraeteEdit/{sportEquipment}', [SportEquipmentController::class, 'edit'])->name('backend.sportEquipment.edit');
    Route::put('/backend/SportgeraeteUpdate/{sportEquipment}', [SportEquipmentController::class, 'update'])->name('backend.sportEquipment.update');

    Route::get('/backend/Course', [CourseController::class, 'index'])->name('backend.course.index');
    Route::get('/backend/CourseEdit/{course}', [CourseController::class, 'edit'])->name('backend.course.edit');
    Route::put('/backend/CourseUpdate/{course}', [CourseController::class, 'update'])->name('backend.course.update');
    Route::get('/backend/CourseSportartPick/{courseId}/{pickSportSectionId}', [CourseController::class, 'pickSportSection'])->name('backend.course.pickSportSection');
    Route::get('/backend/CourseSportartDestroy/{courseId}/{destroySportSectionId}', [CourseController::class, 'destroySportSection'])->name('backend.course.destroySportSection');

    Route::get('/backend/Oranisation', [OrganiserController::class, 'index'])->name('backend.organiser.index');
    Route::get('/backend/OranisationEdit/{organiser}', [OrganiserController::class, 'edit'])->name('backend.organiser.edit');
    Route::put('/backend/OranisationUpdate/{organiser}', [OrganiserController::class, 'update'])->name('backend.organiser.update');
    Route::get('/backend/OranisationSportartPick/{organiserId}/{pickSportSectionId}', [OrganiserController::class, 'pickSportSection'])->name('backend.organiser.pickSportSection');
    Route::get('/backend/OranisationSportartDestroy/{organiserId}/{destroySportSectionId}', [OrganiserController::class, 'destroySportSection'])->name('backend.organiser.destroySportSection');
});

