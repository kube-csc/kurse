<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoursedateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'kursInformation'    => 'nullable',

            // Sportgeräte / Plätze
            'sportgeraetanzahl' => ['nullable', 'integer', 'min:0'],

            // Wenn sportgeraetanzahl > 0, darf reserviert nicht größer sein.
            // (Bei 0 = "maximal/unbegrenzt" greifen andere Limits im Controller.)
            'sportgeraeteReserviert' => ['nullable', 'integer', 'min:0', 'lte:sportgeraetanzahl'],
        ];
    }

    protected function passedValidation(): void
    {
        // Wenn das Feld nicht gesendet oder leer ist, immer auf 0 normalisieren.
        if ($this->input('sportgeraeteReserviert') === null || $this->input('sportgeraeteReserviert') === '') {
            $this->merge(['sportgeraeteReserviert' => 0]);
        }
    }
}
