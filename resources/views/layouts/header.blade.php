<!-- Template Main CSS File abgeändert bei verschiedene Ausgaben -->
@php
    /** @var \App\Models\Organiser $organiser */
    $organiser = app(\App\Http\Controllers\Controller::class)->organiser();

    // Hier wird der Ablageordner fest im Code definiert (nicht in der DB)
    $organiserImagePrefix = 'organisers/';

    $normalize = function (?string $value) use ($organiserImagePrefix): ?string {
        if (empty($value)) {
            return null;
        }

        $v = ltrim($value, '/');

        // Falls irgendwo versehentlich eine URL/Prefix gespeichert wurde
        if (str_starts_with($v, 'storage/')) {
            $v = substr($v, strlen('storage/'));
        }

        // Wenn nur der Dateiname gespeichert wurde, Prefix ergänzen
        if (!str_contains($v, '/')) {
            $v = $organiserImagePrefix . $v;
        }

        return $v;
    };

    $storedGross = $normalize($organiser?->veranstaltungHeader);
    $storedKlein = $normalize($organiser?->veranstaltungHeaderKlein);

    // Wenn nur ein Bild vorhanden ist, soll es an beiden Stellen genutzt werden.
    $storedGross = $storedGross ?: $storedKlein;
    $storedKlein = $storedKlein ?: $storedGross;

    $hasAnyHeader = !empty($storedGross) || !empty($storedKlein);

    $headerGross = $storedGross ? asset('storage/' . $storedGross) : null;
    $headerKlein = $storedKlein ? asset('storage/' . $storedKlein) : $headerGross;
@endphp

@if ($hasAnyHeader)
<style>
    /* Default: Desktop/Tablet */
    #hero {
        width: 100%;
        background: url("{{ $headerGross }}") top center;
        background-size: cover;
    }

    /* Mobile */
    @media (max-width: 767.98px) {
        #hero {
            background: url("{{ $headerKlein }}") top center;
            background-size: cover;
        }
    }
</style>
@endif
