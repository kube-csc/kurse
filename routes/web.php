<?php

use App\Http\Controllers\Backend\CourseController;
use App\Http\Controllers\Backend\CoursedateController;
use App\Http\Controllers\Backend\OrganiserController;
use App\Http\Controllers\Backend\SportEquipmentController;
use App\Http\Controllers\Backend\TripDistanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Backend\TrainerMailController;
use App\Http\Controllers\CourseBooking\CourseParticipantController;
use App\Http\Controllers\CourseBooking\ParticipantMailController;
use App\Http\Controllers\DatenschutzerklärungController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpressumController;
use App\Models\Organiser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\FaqController as BackendFaqController;

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

//Cronjobs
Route::get('/TrainerMail', [TrainerMailController::class, 'trainerMail'])->name('backend.trainerMail');
Route::get('/TeilnehmerMail', [ParticipantMailController::class, 'participantMail'])->name('backend.participantMail');
Route::get('/Training/Planung', [CoursedateController::class, 'cronJobPlanung']);

//Frontend
Route::get('/', [HomeController::class, 'index']);
Route::get('/Startseite', [HomeController::class, 'index']);
Route::get('/Impressum', [ImpressumController::class, 'getImpressumDaten']);
Route::get('/Information/Datenschutzerklaerung', [DatenschutzerklärungController::class, 'getDatenschutzerklärungDaten']);

Route::get('/FAQ', [FaqController::class, 'index'])->name('frontend.faq');

Route::get('/Angebot', [HomeController::class, 'offer'])->name('frontend.offer');
Route::get('/Sportart', [HomeController::class, 'sportType']);
Route::get('/Trainer', [HomeController::class, 'trainer']);
Route::get('/Sportgeraete', [HomeController::class, 'sportUnit']);
Route::get('/Kurse', [HomeController::class, 'coursetype']);
Route::get('/Kurseangebot/{id}', [HomeController::class, 'courseDate'])->name('frontend.course');
Route::get('/Kurseangebot/{coursedate}/Kalender.ics', [CoursedateController::class, 'downloadIcs'])->name('frontend.course.downloadIcs');
Route::get('/Kursbuchung/abmelden', [HomeController::class, 'logout'])->name('frontend.logout');

Route::get('/Kursbuchung/Einbetten', [CourseParticipantController::class, 'embed'])->name('courseBooking.course.embed')->middleware('allowIframes');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'allowIframes'
])->group(function () {
    Route::get('/dashboard', function () {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();

        if (!$organiser) {
            $organiser = new Organiser;
            // Setzen Sie hier die Standardwerte für das Organiser-Objekt
        }

        return view('dashboard', ['organiser' => $organiser]);
    })->name('dashboard');

    Route::get('/Kursbuchung', [CourseParticipantController::class, 'index'])->name('courseBooking.course.index');
    Route::get('/Kursbuchung/Participant', [CourseParticipantController::class, 'indexParticipant'])->name('courseBooking.course.indexParticipant');
    Route::get('/Kursbuchung/edit/{coursedateId}', [CourseParticipantController::class, 'edit'])->name('courseBooking.course.edit');
    Route::put('/Kursbuchung/update/{coursedate}', [CourseParticipantController::class, 'update'])->name('courseBooking.course.update');
    Route::get('/Kursbuchung/buchen/{coursedateId}', [CourseParticipantController::class, 'book'])->name('courseBooking.course.book');
    Route::get('/Kursbuchung/stornieren/{coursedateId}/{courseBookId}', [CourseParticipantController::class, 'destroyBooked'])->name('courseBooking.course.destroyBooked');
});

Route::middleware('admin:admin')->middleware('allowIframes')->group(function () {
    Route::get('admin/login', [AdminController::Class, 'loginForm']);
    Route::post('admin/login', [AdminController::Class, 'store'])->name('admin.login');
});

Route::middleware([
    'auth:sanctum,admin',
    config('jetstream.auth_session'),
    'verified',
    'allowIframes'
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
    Route::put('/backend/CourseDateUpdateGebuchtFirst/{coursedate}', [CoursedateController::class, 'updateBookFirst'])->name('backend.courseDate.updateBookFirst');
    Route::put('/backend/CourseDateStore', [CoursedateController::class, 'store'])->name('backend.courseDate.store');
    Route::get('/backend/CourseDateDestroy/{coursedate}', [CoursedateController::class, 'destroy'])->name('backend.courseDate.destroy');
    Route::get('/backend/CourseDatesportingEquipment/{coursedate}', [CoursedateController::class, 'sportingEquipment'])->name('backend.courseDate.sportingEquipment');
    Route::get('/backend/Book/{coursedateId}', [CoursedateController::class, 'book'])->name('backend.courseDate.book');
    Route::get('/backend/destroyBooked/{coursedateId}/{courseBookId}', [CoursedateController::class, 'destroyBooked'])->name('backend.courseDate.destroyBooked');
    Route::get('/backend/CourseDateEquipmentBooked/{coursedateId}/{sportequipmentId}', [CoursedateController::class, 'equipmentBooked'])->name('backend.courseDate.equipmentBooked');
    Route::get('/backend/CourseDateEquipmentBookedDestroy/{coursedateId}/{kursId}/{sportgeraet}', [CoursedateController::class, 'equipmentBookedDestroy'])->name('backend.courseDate.equipmentBookedDestroy');
    Route::get('/backend/CourseDateTrainerPick{coursedateId}', [CoursedateController::class, 'trainerRegister'])->name('backend.courseDate.trainerRegister');
    Route::get('/backend/CourseDateTrainerDestroy/{coursedateId}', [CoursedateController::class, 'trainerDestroy'])->name('backend.courseDate.trainerDestroy');
    Route::get('/backend/CourseBockedInformation/{coursedate}', [CoursedateController::class, 'CourseBockedInformation'])->name('backend.courseDate.CourseBockedInformation');
    Route::get('/backend/CourseDateIcs/{coursedate}', [CoursedateController::class, 'downloadIcs'])->name('backend.courseDate.downloadIcs');

    Route::get('/backend/TripDistance', [TripDistanceController::class, 'index'])->name('backend.tripDistance.index');
    Route::get('/backend/TripDistance/Report', [TripDistanceController::class, 'report'])->name('backend.tripDistance.report');
    Route::get('/backend/TripDistance/{coursedate}', [TripDistanceController::class, 'show'])->name('backend.tripDistance.show');
    Route::put('/backend/TripDistance/Coursedate/{coursedate}', [TripDistanceController::class, 'updateCoursedateDistance'])->name('backend.tripDistance.updateCoursedateDistance');
    Route::put('/backend/TripDistance/Participant/{courseParticipantBooked}', [TripDistanceController::class, 'updateParticipantDistance'])->name('backend.tripDistance.updateParticipantDistance');
    Route::put('/backend/TripDistance/Trainer/{coursedate}/{userId}', [TripDistanceController::class, 'updateTrainerDistance'])->name('backend.tripDistance.updateTrainerDistance');

    Route::get('/backend/Sportgeraete', [SportEquipmentController::class, 'index'])->name('backend.sportEquipment.index');
    Route::get('/backend/SportgeraeteAlle', [SportEquipmentController::class, 'indexAll'])->name('backend.sportEquipment.indexAll');
    Route::get('/backend/SportgeraeteCreate', [SportEquipmentController::class, 'create'])->name('backend.sportEquipment.create');
    Route::post('/backend/SportgeraeteStore', [SportEquipmentController::class, 'store'])->name('backend.sportEquipment.store');
    Route::get('/backend/SportgeraeteEdit/{sportEquipment}', [SportEquipmentController::class, 'edit'])->name('backend.sportEquipment.edit');
    Route::put('/backend/SportgeraeteUpdate/{sportEquipment}', [SportEquipmentController::class, 'update'])->name('backend.sportEquipment.update');
    Route::put('/backend/SportgeraeteBildDestroy/{sportEquipment}', [SportEquipmentController::class, 'destroyImage'])->name('backend.sportEquipment.destroyImage');

    Route::get('/backend/Course', [CourseController::class, 'index'])->name('backend.course.index');
    Route::get('/backend/Course/IFrameGenerator', [CourseController::class, 'iframeGenerator'])->name('backend.course.iframe_generator');
    Route::get('/backend/CourseCreate', [CourseController::class, 'create'])->name('backend.course.create');
    Route::post('/backend/CourseStore', [CourseController::class, 'store'])->name('backend.course.store');
    Route::get('/backend/CourseEdit/{course}', [CourseController::class, 'edit'])->name('backend.course.edit');
    Route::put('/backend/CourseUpdate/{course}', [CourseController::class, 'update'])->name('backend.course.update');
    Route::get('/backend/CourseSportartPick/{courseId}/{pickSportSectionId}', [CourseController::class, 'pickSportSection'])->name('backend.course.pickSportSection');
    Route::get('/backend/CourseSportartDestroy/{courseId}/{destroySportSectionId}', [CourseController::class, 'destroySportSection'])->name('backend.course.destroySportSection');

    Route::get('/backend/Oranisation', [OrganiserController::class, 'index'])->name('backend.organiser.index');
    Route::get('/backend/OranisationEdit/{organiser}', [OrganiserController::class, 'edit'])->name('backend.organiser.edit');
    Route::put('/backend/OranisationUpdate/{organiser}', [OrganiserController::class, 'update'])->name('backend.organiser.update');
    Route::delete('/backend/Oranisation/{organiser}/veranstaltungHeader', [OrganiserController::class, 'destroyVeranstaltungHeader'])
        ->name('backend.organiser.destroyVeranstaltungHeader');
    Route::delete('/backend/Oranisation/{organiser}/veranstaltungHeaderKlein', [OrganiserController::class, 'destroyVeranstaltungHeaderKlein'])
        ->name('backend.organiser.destroyVeranstaltungHeaderKlein');
    Route::get('/backend/OranisationSportartPick/{organiserId}/{pickSportSectionId}', [OrganiserController::class, 'pickSportSection'])->name('backend.organiser.pickSportSection');
    Route::get('/backend/OranisationSportartDestroy/{organiserId}/{destroySportSectionId}', [OrganiserController::class, 'destroySportSection'])->name('backend.organiser.destroySportSection');

    // FAQ Backend
    Route::get('/backend/FAQ/{organiser}', [BackendFaqController::class, 'index'])->name('faq.index');
    Route::get('/backend/FAQ/{organiser}/Create', [BackendFaqController::class, 'create'])->name('faq.create');
    Route::post('/backend/FAQ/{organiser}/Store', [BackendFaqController::class, 'store'])->name('faq.store');
    Route::get('/backend/FAQ/{organiser}/Edit/{faq}', [BackendFaqController::class, 'edit'])->name('faq.edit');
    Route::post('/backend/FAQ/{organiser}/Update/{faq}', [BackendFaqController::class, 'update'])->name('faq.update');
    Route::post('/backend/FAQ/{organiser}/Destroy/{faq}', [BackendFaqController::class, 'destroy'])->name('faq.destroy');

    Route::get('/backend/FAQ/{organiser}/Aktiv/{faq}', [BackendFaqController::class, 'aktiv'])->name('faq.aktiv');
    Route::get('/backend/FAQ/{organiser}/Inaktiv/{faq}', [BackendFaqController::class, 'inaktiv'])->name('faq.inaktiv');

    Route::get('/backend/FAQ/{organiser}/Up/{faq}', [BackendFaqController::class, 'up'])->name('faq.up');
    Route::get('/backend/FAQ/{organiser}/Down/{faq}', [BackendFaqController::class, 'down'])->name('faq.down');
    Route::get('/backend/FAQ/{organiser}/Category/Up/{faq}', [BackendFaqController::class, 'categoryUp'])->name('faq.category.up');
    Route::get('/backend/FAQ/{organiser}/Category/Down/{faq}', [BackendFaqController::class, 'categoryDown'])->name('faq.category.down');
});
