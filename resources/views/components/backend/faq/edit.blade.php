<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            FAQ bearbeiten
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open"><box-icon name='info-circle'></box-icon></button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    Hier kannst du die FAQ-Inhalte bearbeiten. Mit der Checkbox „Nur für diesen Organisator sichtbar“ steuerst du, ob der Eintrag global oder organiser-spezifisch ist.
                </p>
            </div>
            <div x-data="{ openHelpEdit: false }" class="text-left">
                <button @click="openHelpEdit = !openHelpEdit">
                    {{ __('backend.Edit help HTML button') }}
                </button>
                <div class="help-box" x-show="openHelpEdit" @click.away="openHelpEdit = false">
                    <p class="help-text">
                        {!! __('backend.Edit help HTML') !!}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="main-box">
        <div class="box">
            <form action="{{ route('faq.update', ['organiser' => $selectedOrganiserId ?? $organiser->id, 'faq' => $faq->id]) }}" method="post">
                @csrf

                <div class="form-group">
                    <div class="form-card">
                        @include('components.backend.faq._form', [
                            'faq' => $faq,
                            'categories' => $categories ?? collect(),
                        ])
                    </div>
                </div>
                <div class="form-footer">
                    <a href="{{ route('faq.index', ['organiser' => $selectedOrganiserId ?? $organiser->id]) }}" class="form-button">
                        {{ __('main.back') }}
                    </a>
                    <button type="submit" class="form-button">
                        {{ __('main.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
