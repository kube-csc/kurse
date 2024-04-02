<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                    {{ __('backend.Sport Equipment') }}
            </h2>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">
        <div class="dashboard-flexbox">
            @foreach($sportEquipments as $sportEquipment)
                <div class="dashboard-flexbox-b1-2">
                    <div class="dashboard-flexbox-text">
                        <div class="dasboard-iconbox">
                            <a href="{{ route('backend.sportEquipment.edit', $sportEquipment->id) }}">
                                <box-icon name='edit'></box-icon>
                            </a>
                        </div>
                        <label class="label">Sportgerät:</label>
                        {{ $sportEquipment->sportgeraet }}<br>
                        <label class="label">Datum der Anschaffung:</label>
                        {{ date('d.m.Y', strtotime($sportEquipment->anschafdatum)) }}
                        @if($sportEquipment->verschrottdatum != null)
                             <label class="label">Datum der Verschrottung:</label>
                             {{ date('d.m.Y', strtotime($sportEquipment->verschrottdatum)) }}
                        @endif
                        @if($sportEquipment->bild != Null)
                             <label class="label">Bild:</label>
                             <img src="/storage/sportgeraet/{{ $sportEquipment->bild }}" width="100%" alt="{{ $sportEquipment->sportgeraet }}"/><br>
                        @endif
                        @if($sportEquipment->laenge != null)
                            <label class="label">Länge in Meter:</label>
                            {{ $sportEquipment->laenge }}<br>
                        @endif
                        @if($sportEquipment->breite != null)
                            <label class="label">Breite in Meter:</label>
                            {{ $sportEquipment->breite }}<br>
                        @endif
                        @if($sportEquipment->hoehe != null)
                            <label class="label">Höhe in Meter:</label>
                            {{ $sportEquipment->hoehe }}<br>
                        @endif
                        @if($sportEquipment->gewicht != null)
                            <label class="label">Gewicht in kg:</label>
                            {{ $sportEquipment->gewicht }}<br>
                        @endif
                        @if($sportEquipment->tragkraft != null)
                            <label class="label">Tragkraft in kg:</label>
                            {{ $sportEquipment->tragkraft }}<br>
                        @endif
                        <label class="label">Sportleranzahl:</label>
                        {{ $sportEquipment->sportleranzahl }}<br>
                        @if($sportEquipment->typ != null)
                            <label class="label">Beschreibung:</label>
                            {!! $sportEquipment->typ !!}<br>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>


