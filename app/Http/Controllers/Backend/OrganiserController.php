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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'veranstaltung'              => 'required',
            'veranstaltungDomain'        => 'nullable',
            // bleibt optional und wird nur gesetzt, wenn wirklich eine Datei hochgeladen wurde
            'veranstaltungHeader'        => 'nullable',
            'veranstaltungHeaderKlein'   => 'nullable',
            'sportartUeberschrift'       => 'nullable',
            'materialUeberschrift'       => 'nullable',
            'trainerUeberschrift'        => 'nullable',
            'kurseUeberschrift'          => 'nullable'
        ]);

        $deleteOldHeaderFile = function (?string $storedValue): void {
            if (empty($storedValue)) {
                return;
            }

            $old = ltrim((string)$storedValue, '/');

            // alte Formate abfangen
            if (Str::startsWith($old, 'storage/')) {
                $old = Str::after($old, 'storage/');
            }
            if (Str::startsWith($old, 'organisers/')) {
                $old = Str::after($old, 'organisers/');
            }

            // wir speichern künftig nur den Dateinamen; Storage-Pfad ist fest
            $oldPath = 'organisers/' . $old;

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        };

        $storeHeaderFile = function (string $inputName) use ($request, $organiser): ?string {
            if (!$request->hasFile($inputName)) {
                return null;
            }

            $file = $request->file($inputName);

            // Dateiname:
            // - Gross: organisers{ID}_{hash6}.{ext}
            // - Klein: organisers{ID}_k_{hash6}.{ext}
            $hash6 = Str::lower(Str::random(6));
            $ext = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
            $isKlein = $inputName === 'veranstaltungHeaderKlein';

            $suffix = $isKlein ? ('k_' . $hash6) : $hash6;
            $filename = 'organisers' . $organiser->id . '_' . $suffix . '.' . $ext;

            $file->storeAs('organisers', $filename, 'public');

            // In der DB nur den Dateinamen speichern
            return $filename;
        };

        // Header-Bild (Desktop/Tablet) Upload
        if ($request->hasFile('veranstaltungHeader')) {
            $filename = $storeHeaderFile('veranstaltungHeader');
            $deleteOldHeaderFile($organiser->veranstaltungHeader);
            $organiserData['veranstaltungHeader'] = $filename;
        } else {
            unset($organiserData['veranstaltungHeader']);
        }

        // Header-Bild klein (Mobile) Upload
        if ($request->hasFile('veranstaltungHeaderKlein')) {
            $filename = $storeHeaderFile('veranstaltungHeaderKlein');
            $deleteOldHeaderFile($organiser->veranstaltungHeaderKlein);
            $organiserData['veranstaltungHeaderKlein'] = $filename;
        } else {
            unset($organiserData['veranstaltungHeaderKlein']);
        }

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

    /**
     * Headerbild (Desktop/Tablet) löschen: Datei entfernen + DB-Feld auf null setzen.
     */
    public function destroyVeranstaltungHeader(Organiser $organiser)
    {
        $deleteOldHeaderFile = function (?string $storedValue): void {
            if (empty($storedValue)) {
                return;
            }

            $old = ltrim((string)$storedValue, '/');
            if (Str::startsWith($old, 'storage/')) {
                $old = Str::after($old, 'storage/');
            }
            if (Str::startsWith($old, 'organisers/')) {
                $old = Str::after($old, 'organisers/');
            }

            $oldPath = 'organisers/' . $old;

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        };

        $deleteOldHeaderFile($organiser->veranstaltungHeader);

        $organiser->update([
            'veranstaltungHeader' => null,
            'bearbeiter_id' => Auth::user()->id,
        ]);

        self::success('Headerbild wurde gelöscht.');

        return redirect()->route('backend.organiser.edit', $organiser->id);
    }

    /**
     * Headerbild klein (Mobile) löschen: Datei entfernen + DB-Feld auf null setzen.
     */
    public function destroyVeranstaltungHeaderKlein(Organiser $organiser)
    {
        $deleteOldHeaderFile = function (?string $storedValue): void {
            if (empty($storedValue)) {
                return;
            }

            $old = ltrim((string)$storedValue, '/');
            if (Str::startsWith($old, 'storage/')) {
                $old = Str::after($old, 'storage/');
            }
            if (Str::startsWith($old, 'organisers/')) {
                $old = Str::after($old, 'organisers/');
            }

            $oldPath = 'organisers/' . $old;

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        };

        $deleteOldHeaderFile($organiser->veranstaltungHeaderKlein);

        $organiser->update([
            'veranstaltungHeaderKlein' => null,
            'bearbeiter_id' => Auth::user()->id,
        ]);

        self::success('Kleines Headerbild wurde gelöscht.');

        return redirect()->route('backend.organiser.edit', $organiser->id);
    }
}
