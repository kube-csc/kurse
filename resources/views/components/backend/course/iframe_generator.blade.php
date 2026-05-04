<x-app-layout>
    <x-slot name="header">
        <h2 class="header-h2">
            {{ __('backend.IFrame Generator') }}
        </h2>
        <div x-data="{ open: false }" class="dasboard-iconbox">
            <button class="dasboard-iconbox-a" @click="open = !open">
                <box-icon name='info-circle'></box-icon>
            </button>
            <div class="help-box" x-show="open" @click.away="open = false">
                <p class="help-text">
                    {!! __('backend.IFrame Generator Help') !!}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="main-box" x-data="{
        selectedIds: [],
        domain: '{{ request()->getSchemeAndHttpHost() }}',
        copied: false,
        get iframeCode() {
            let url = this.domain + '/Kursbuchung/Einbetten';
            if (this.selectedIds.length > 0) {
                url += '?course_ids=' + this.selectedIds.join(',');
            }
            return `<iframe
    src='${url}'
    width='100%'
    height='700'
    style='border:0; max-width:100%;'
    loading='lazy'
    title='Kursbuchung'>
</iframe>`;
        },
        copyCode() {
            const textarea = document.createElement('textarea');
            textarea.value = this.iframeCode;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            this.copied = true;
            setTimeout(() => { this.copied = false }, 2000);
        }
    }">
        <div class="box">
            <div class="form-group">
                <div class="form-card">
                    <div class="form-field">
                        <label class="form-label">{{ __('backend.IFrame Generator Select Courses') }}</label>
                        <div class="dashboard-flexbox">
                            @foreach($courses as $course)
                                <div class="dashboard-flexbox-b1-2">
                                    <div class="dashboard-flexbox-text">
                                        <div class="flex justify-between items-start">
                                            <div class="label">
                                                {{ $course->kursName }}
                                            </div>
                                            <input type="checkbox" value="{{ $course->id }}" x-model="selectedIds" class="w-5 h-5">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="iframeCodeDisplay" class="form-label">{{ __('backend.IFrame Generator Generated Code') }}</label>
                        <textarea id="iframeCodeDisplay" class="form-input-textarea font-mono text-xs" rows="8" x-text="iframeCode" readonly></textarea>
                    </div>
                </div>
            </div>
            <div class="form-footer flex justify-between items-center">
                <a href="{{ route('backend.course.index') }}" class="form-button">
                    {{ __('main.back') }}
                </a>
                <div class="flex items-center gap-4">
                    <span x-show="copied" x-transition.opacity class="text-green-600 font-bold">
                        {{ __('backend.IFrame Generator Copy Code Success') ?? __('Kopiert!') }}
                    </span>
                    <button type="button" class="form-button" @click="copyCode()">
                        {{ __('backend.IFrame Generator Copy Code') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
