<?php

namespace App\Http\Requests;

use App\Models\Course;
use App\Models\Organiser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCoursedateRequest extends FormRequest
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
            'kurslaenge'          => 'required',
            'kursInformation'     => 'nullable',
            'course_id'           => ['required', 'integer', $this->courseValidationRule()],
        ];
    }

    /**
     * Custom validation rule to check if trainer has the required sport section for the course
     */
    private function courseValidationRule()
    {
        return function ($attribute, $value, $fail) {
            $course = Course::find($value);
            $organiserId = $this->resolveOrganiserId();

            if (!$course) {
                $fail('Der gewählte Kurs existiert nicht.');
                return;
            }

            if (!$organiserId) {
                $fail('Die zugehörige Organisation konnte nicht ermittelt werden.');
                return;
            }

            if ((int) $course->organiser_id !== (int) $organiserId) {
                $fail('Der gewählte Kurs gehört nicht zur aktuellen Organisation.');
                return;
            }

            if (!Course::isAssignableToUserInOrganiser((int) $value, (int) $organiserId, (int) Auth::id())) {
                $fail('Du bist für dieses Kursangebot nicht freigeschaltet.');
            }
        };
    }

    private function resolveOrganiserId(): ?int
    {
        if ($this->filled('organiser_id')) {
            return (int) $this->input('organiser_id');
        }

        $organiser = Organiser::where('veranstaltungDomain', $this->server('HTTP_HOST'))->first();

        return $organiser?->id ?? Organiser::find(1)?->id;
    }
}
