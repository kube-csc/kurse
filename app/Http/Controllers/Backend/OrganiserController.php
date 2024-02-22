<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganiserRequest;
use App\Http\Requests\UpdateOrganiserRequest;
use App\Models\Organiser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrganiserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organisers = Organiser::orderBy('veranstalter')
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

        return view('components.backend.organiser.edit', compact('organiser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganiserRequest $request, Organiser $organiser)
    {
        //ToDo: Verbessern der Validierung
        //$data = $request->validated();

        $data = $request->validate([
            'veranstalter' => 'required',
            'veranstalterBeschreibung' => 'nullable'
        ]);

        $data['bearbeiter_id'] = Auth::user()->id;
        $data['updated_at'] = Carbon::now();

        $organiser->update($data);

        self::success('Veranstalterdaten erfolgreich geändert');

        return redirect()->route('backend.organiser.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organiser $organiser)
    {
        //
    }
}
