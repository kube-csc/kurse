<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSportEquipmentRequest;
use App\Http\Requests\UpdateSportEquipmentRequest;
use App\Models\Organiser;
use App\Models\SportEquipment;
use App\Models\SportSection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SportEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organiser = $this->organiser();

        $sportEquipments = Organiser::where('organisers.id', $organiser->id)
            ->join('organiser_sport_section', 'organisers.id', '=', 'organiser_sport_section.organiser_id')
            ->join('sport_equipment', 'organiser_sport_section.sport_section_id', '=', 'sport_equipment.sportSection_id')
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
        $organiser = $this->organiser();

        $sportSections = SportSection::join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_sections.id')
            ->where('organiser_sport_section.organiser_id', $organiser->id)
            ->orderBy('sport_sections.abteilung')
            ->select('sport_sections.*')
            ->get();

        return view('components.backend.sportEquipment.create', compact('sportSections', 'organiser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSportEquipmentRequest $request)
    {
        $data = $request->validated();

        // Defaults für Pflichtfelder (falls im Formular nicht gepflegt)
        $data['privat'] = $data['privat'] ?? '0';

        // DB-Spalte ist nicht nullable
        $data['bild'] = '';

        $data['autor_id'] = Auth::user()->id;
        $data['bearbeiter_id'] = Auth::user()->id;
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        $sportEquipment = SportEquipment::create($data);

        if ($request->hasFile('bild')) {
            $ext = strtolower($request->file('bild')->getClientOriginalExtension() ?: 'bin');
            $suffix = bin2hex(random_bytes(2)); // 4 Hex-Zeichen
            $filename = 'equipment' . $sportEquipment->id . '_' . $suffix . '.' . $ext;

            $request->file('bild')->storeAs('sportgeraete', $filename, 'public');

            $sportEquipment->update([
                'bild' => $filename,
                'bearbeiter_id' => Auth::user()->id,
            ]);
        }

        self::success('Sportgerät wurde erfolgreich angelegt.');

        return redirect()->route('backend.sportEquipment.index');
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

        $organiser = $this->organiser();

        // Wenn wir aus der Verwaltungsansicht kommen, sollen alle Sportarten/Abteilungen auswählbar sein.
        $fromAll = request()->boolean('fromAll');

        if ($fromAll) {
            $sportSections = SportSection::orderBy('abteilung')->get();
        } else {
            $sportSections = SportSection::join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_sections.id')
                ->where('organiser_sport_section.organiser_id', $organiser->id)
                ->orderBy('sport_sections.abteilung')
                ->select('sport_sections.*')
                ->get();
        }

        return view('components.backend.sportEquipment.edit', compact('sportEquipment', 'sportSections', 'organiser', 'fromAll'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSportEquipmentRequest $request, SportEquipment $sportEquipment)
    {
        $data = $request->validate([
            'sportgeraet'     => 'required|string|max:255',
            'sportSection_id' => 'required|integer|exists:sport_sections,id',
            'anschafdatum'    => 'required|date',
            'verschrottdatum' => 'nullable|date',
            'laenge'          => 'required',
            'breite'          => 'required',
            'hoehe'           => 'required',
            'gewicht'         => 'required',
            'tragkraft'       => 'required',
            'typ'             => 'nullable',
            'sportleranzahl'  => 'min:1',
            'bild'            => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,svg|mimetypes:image/jpeg,image/png,image/webp,image/gif,image/bmp,image/svg+xml|max:5120',
        ]);

        if ($request->hasFile('bild')) {
            // altes Bild löschen (best-effort)
            if (!empty($sportEquipment->bild)) {
                Storage::disk('public')->delete('sportgeraete/' . $sportEquipment->bild);
            }

            $ext = strtolower($request->file('bild')->getClientOriginalExtension() ?: 'bin');
            $suffix = bin2hex(random_bytes(2)); // 4 Hex-Zeichen
            $filename = 'equipment' . $sportEquipment->id . '_' . $suffix . '.' . $ext;

            $request->file('bild')->storeAs('sportgeraete', $filename, 'public');
            $data['bild'] = $filename;
        }

        $data['bearbeiter_id'] = Auth::user()->id;
        $data['updated_at'] = Carbon::now();

        $sportEquipment->update($data);

        self::success('Sportgerätedaten erfolgreich geändert');

        if ($request->boolean('fromAll')) {
            return redirect()->route('backend.sportEquipment.indexAll');
        }

        return redirect()->route('backend.sportEquipment.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SportEquipment $sportEquipment)
    {
        //
    }

    public function destroyImage(SportEquipment $sportEquipment)
    {
        if (!empty($sportEquipment->bild)) {
            Storage::disk('public')->delete('sportgeraete/' . $sportEquipment->bild);
        }

        $sportEquipment->update([
            'bild' => '',
            'bearbeiter_id' => Auth::user()->id,
        ]);

        self::success('Bild wurde erfolgreich gelöscht.');

        $params = [];
        if (request()->boolean('fromAll')) {
            $params['fromAll'] = 1;
        }

        return redirect()->route('backend.sportEquipment.edit', array_merge(['sportEquipment' => $sportEquipment->id], $params));
    }
}
