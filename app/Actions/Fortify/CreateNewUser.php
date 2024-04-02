<?php

namespace App\Actions\Fortify;

use App\Models\CourseParticipant as User;
use App\Models\Organiser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['name'] = $input['vorname'];

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:course_participants'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'vorname' => 'required|string|max:255',
            'nachname' => 'required|string|max:255',
            'telefon' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'pruefsumme' => ['required', 'integer', 'size:99'],
        ])->validate();

        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            $organiser = Organiser::find(1);
        }

        return User::create([
            'name'         => $input['name'],
            'email'        => $input['email'],
            'password'     => Hash::make($input['password']),
            'organiser_id' => $organiser->id,
            'nachname'     => $input['nachname'],
            'vorname'      => $input['vorname'],
            'telefon'      => $input['telefon'],
            'nachricht'    => 0,
            'status'       => 0,
        ]);
    }
}
