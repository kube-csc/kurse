<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganiserRequest;
use App\Http\Requests\UpdateOrganiserRequest;
use App\Models\Organiser;
use App\Models\Organiserinformation;
use App\Models\SportSection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrganiserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organisers = Organiser::orderBy('veranstaltung')
            ->get();

        return view('components.backend.organiser.index', compact('organisers'));
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
    public function store(StoreOrganiserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Organiser $organiser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $organiser = Organiser::find($id);

        //ToDo: Verbessern der Abfrage
        $pickedSportSections = SportSection::join('organiser_sport_section', 'organiser_sport_section.sport_section_id', '=', 'sport_sections.id')
            ->where('organiser_sport_section.organiser_id', $organiser->id)
            ->orderBy('abteilung')
            ->get();

        $sportSections = SportSection::orderBy('abteilung')->get();
        $pickedSportSectionIds = $pickedSportSections->pluck('sport_section_id');
        $sportSections = $sportSections->whereNotIn('id', $pickedSportSectionIds);

        return view('components.backend.organiser.edit', compact('organiser', 'sportSections', 'pickedSportSections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganiserRequest $request, Organiser $organiser)
    {
        //ToDo: Verbessern der Validierung
        //$data = $request->validated();

        $organiserinformationData = $request->validate([
            'veranstaltungBeschreibungLang' => 'nullable',
            'veranstaltungBeschreibungKurz' => 'nullable',
            'sportartBeschreibungLang'      => 'nullable',
            'sportartBeschreibungKurz'      => 'nullable',
            'materialBeschreibungLang'      => 'nullable',
            'materialBeschreibungKurz'      => 'nullable',
            'mitgliedschaftKurz'            => 'nullable',
            'mitgliedschaftLang'            => 'nullable',
            'veranstaltungDomain'           => 'nullable',
            'terminInformation'             => 'nullable',
            'keineKurse'                    => 'nullable'
        ]);

        $organiserinformationData['bearbeiter_id'] = Auth::user()->id;
        $organiserinformationData['updated_at'] = Carbon::now();

        $organiserinformation = Organiserinformation::find($organiser->id);

        $organiserinformation->update($organiserinformationData);

        $organiserData = $request->validate([
            'veranstaltung'         => 'required',
            'veranstaltungDomain'   => 'nullable',
            'veranstaltungHeader'   => 'nullable',
            'sportartUeberschrift'  => 'nullable',
            'materialUeberschrift'  => 'nullable',
            'trainerUeberschrift'   => 'nullable'
        ]);

        $organiserData['bearbeiter_id'] = Auth::user()->id;
        $organiserData['updated_at'] = Carbon::now();

        $organiser->update($organiserData);

        self::success('Daten der Veranstaltung erfolgreich geändert');

        return redirect()->route('backend.organiser.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organiser $organiser)
    {
        //
    }

    public function pickSportSection($organiserId, $pickSportSectionId)
    {
        $organiser = Organiser::find($organiserId);
        $organiser->sportSection()->attach($pickSportSectionId);

        self::success('Sportart wurde erfolgreich zugeordnet.');

        return redirect()->route('backend.organiser.edit', $organiserId);
    }

    public function destroySportSection($organiserId, $destroySportSectionId)
    {
        $organiser = Organiser::find($organiserId);
        $organiser->sportSection()->detach($destroySportSectionId);

        self::success('Sportart wurde erfolgreich entfernt.');

        return redirect()->route('backend.organiser.edit', $organiserId);
    }
}
