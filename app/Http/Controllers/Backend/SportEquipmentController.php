<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSportEquipmentRequest;
use App\Http\Requests\UpdateSportEquipmentRequest;
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
        // Alle Sportgeräte aus der Datenbank abrufen
        $sportEquipments = SportEquipment::where('sportSection_id' , env('KURS_ABTEILUNG', 1))
            ->orderBy('anschafdatum',)
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
