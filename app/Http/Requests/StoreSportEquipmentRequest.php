<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSportEquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'sportgeraet'     => 'required|string|max:255',
            'sportSection_id' => 'required|integer|exists:sport_sections,id',
            'anschafdatum'    => 'required|date',
            'verschrottdatum' => 'nullable|date',
            'sportleranzahl'  => 'required|integer|min:1',
            'laenge'          => 'nullable',
            'breite'          => 'nullable',
            'hoehe'           => 'nullable',
            'gewicht'         => 'nullable',
            'tragkraft'       => 'nullable',
            'typ'             => 'nullable',
            'privat'          => 'nullable',
            'mitgliedprivat_id' => 'nullable|integer',
            'visible'         => 'nullable|boolean',
            'bild'            => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,bmp,svg|mimetypes:image/jpeg,image/png,image/webp,image/gif,image/bmp,image/svg+xml|max:5120',
        ];
    }
}
