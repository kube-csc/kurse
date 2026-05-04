<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            FAQ anlegen
        </h2>
    </x-slot>

    <div class="main-box">
        <div class="box">
            <form action="{{ route('faq.store', ['organiser' => $selectedOrganiserId ?? $organiser->id]) }}" method="post">
                @csrf

                <div class="form-group">
                    <div class="form-card">
                        @include('components.backend.faq._form', [
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
