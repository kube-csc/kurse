<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganiserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (auth()->check())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'veranstaltungHeader'            => 'nullable',
            'veranstaltungBeschreibungLang'  => 'nullable',
            'veranstaltungBeschreibungKurz'  => 'nullable',
            'sportartBeschreibungLang'       => 'nullable',
            'sportartBeschreibungKurz'       => 'nullable',
            'materialBeschreibungLang'       => 'nullable',
            'materialBeschreibungKurz'       => 'nullable',
            'keineKurse'                     => 'nullable',
            'terminInformation'              => 'nullable',
            'mitgliedschaftKurz'             => 'nullable',
            'mitgliedschaftLang'             => 'nullable',
            'trainerUeberschrift'            => 'required',
            'sportartUeberschrift'           => 'required',
            'materialUeberschrift'           => 'required',
        ];
    }
}
