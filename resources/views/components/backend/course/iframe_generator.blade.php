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
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(this.iframeCode).then(() => {
                    this.copied = true;
                    setTimeout(() => { this.copied = false }, 2000);
                });
            } else {
                const textarea = document.createElement('textarea');
                textarea.value = this.iframeCode;
                textarea.style.position = 'fixed';
                textarea.style.left = '-9999px';
                textarea.style.top = '0';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();
                try {
                    document.execCommand('copy');
                    this.copied = true;
                    setTimeout(() => { this.copied = false }, 2000);
                } catch (err) {
                    console.error('Fallback: Oops, unable to copy', err);
                }
                document.body.removeChild(textarea);
            }
        }
    }">
        <div class="box">
            <div class="form-group">
                <div class="form-card">
                    <div class="form-field">
                        <label class="form-label">{{ __('backend.IFrame Generator Select Courses') }}</label>
                        <div class="dashboard-flexbox">
                            @foreach($courses as $course)
                                <div class="dashboard-flexbox-b1-2 mb-2">
                                    <label class="dashboard-flexbox-text flex justify-between items-center cursor-pointer p-4 border rounded-md hover:bg-gray-50">
                                        <span class="label mb-0">
                                            {{ $course->kursName }}
                                        </span>
                                        <input type="checkbox" value="{{ $course->id }}" x-model="selectedIds" class="w-6 h-6 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </label>
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
            <div class="form-footer flex flex-col sm:flex-row justify-between items-center gap-4">
                <a href="{{ route('backend.course.index') }}" class="form-button w-full sm:w-auto text-center">
                    {{ __('main.back') }}
                </a>
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <span x-show="copied" x-transition.opacity class="text-green-600 font-bold order-2 sm:order-1">
                        {{ __('backend.IFrame Generator Copy Code Success') ?? __('Kopiert!') }}
                    </span>
                    <button type="button" class="form-button w-full sm:w-auto order-1 sm:order-2" @click="copyCode()">
                        {{ __('backend.IFrame Generator Copy Code') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
