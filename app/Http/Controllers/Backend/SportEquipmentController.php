<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSportEquipmentRequest;
use App\Http\Requests\UpdateSportEquipmentRequest;
use App\Models\Organiser;
use App\Models\SportEquipment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SportEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organiser = Organiser::where('veranstalterDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            // Replace 'default' with the actual default Organiser ID or another query to fetch the default Organiser
            $organiser = Organiser::find(1);
        }

        $sportEquipments = SportEquipment::join('organiser_sport_section', 'sport_equipment.sportSection_id', '=', 'organiser_sport_section.sport_section_id')
            ->join ('organisers', 'organiser_sport_section.organiser_id', '=', 'organisers.id')
            ->where('organisers.id', $organiser->id)
            ->orderBy('anschafdatum',)
            ->orderBy('sportgeraet')
            ->get();

        // Die Ansicht 'sportEquipment.index' rendern und die Sportgeräte übergeben
        return view('components.backend.sportEquipment.index', compact('sportEquipments'));
    }

    public function indexAll()
    {
        // ToDo: Die Sportgeräte der Verwaltung ausgeben
        $sportEquipments = SportEquipment::orderBy('anschafdatum',)
            ->orderBy('sportgeraet')
            ->get();

        // Die Ansicht 'sportEquipment.index' rendern und die Sportgeräte übergeben
        return view('components.backend.sportEquipment.index', compact('sportEquipments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSportEquipmentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SportEquipment $sportEquipment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SportEquipment $sportEquipment)
    {
        $sportEquipment = SportEquipment::find($sportEquipment->id);

        return view('components.backend.sportEquipment.edit', compact('sportEquipment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSportEquipmentRequest $request, SportEquipment $sportEquipment)
    {
        //ToDO: Verbessern der Valentierung
        $data = $request->validate([
            'sportgeraet' => 'required|string|max:255',
            'anschafdatum' => 'required|date',
            'verschrottdatum' => 'nullable|date',
            'laenge' => 'nullable',
            'breite' => 'nullable',
            'hoehe' => 'nullable',
            'gewicht' => 'nullable',
            'tragkraft' => 'nullable',
            'typ' => 'nullable',
        ]);

        $data['bearbeiter_id'] = Auth::user()->id;
        $data['updated_at'] = Carbon::now();

        $sportEquipment->update($data);

        self::success('Sportgerätedaten erfolgreich geändert');

        return redirect()->route('backend.sportEquipment.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SportEquipment $sportEquipment)
    {
        //
    }
}
