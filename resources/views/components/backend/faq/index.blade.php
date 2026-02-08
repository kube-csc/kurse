<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <h2 class="header-h2">
                FAQ
            </h2>
            <div class="dasboard-iconbox w-12 ml-4">
                <a href="{{ route('faq.create', ['organiser' => $selectedOrganiserId]) }}" title="Neu">
                    <box-icon name='plus'></box-icon>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="main-box">
        @if (session()->has('success'))
            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm m-2">
                {!! session('success') !!}
            </div>
        @endif

        @php
            $faqGroups = $faqGroups ?? collect();
        @endphp

        <div class="dashboard-flexbox">
            @forelse($faqGroups as $group)
                @php
                    /** @var \Illuminate\Support\Collection $faqsByCategory */
                    $faqsByCategory = $group['faqs'] ?? collect();

                    $sortedCategories = $faqsByCategory->sortBy(function ($items, $category) {
                        $first = $items->first();
                        return [
                            (int) ($first->category_sort_order ?? 0),
                            (string) $category,
                        ];
                    });

                    // Für Up/Down-Enabled-Checks brauchen wir stabile Indizes.
                    $sortedCategoryKeys = $sortedCategories->keys()->values();
                    $accordionId = 'backend-faq-accordion-' . md5(($group['title'] ?? 'faq') . '|' . ($group['organisers_id'] ?? 'general'));
                @endphp

                {{-- Jede Gruppe bekommt ihre eigene Box (volle Breite) --}}
                <div class="basis-full" data-faq-accordion="{{ $accordionId }}">
                    <div class="dashboard-flexbox-b1-2" style="max-width: 100%; flex-basis: 100%;">
                        <div class="dashboard-flexbox-text">
                            <div class="dasboard-iconbox" style="display:flex; align-items:center; justify-content: space-between;">
                                <span class="font-bold text-lg">{{ $group['title'] ?? 'FAQ' }}</span>
                                <span class="text-xs text-gray-600">Kategorien: {{ $faqsByCategory->count() }}</span>
                            </div>

                            <div class="mt-3">
                                @forelse($sortedCategories as $category => $items)
                                    @php
                                        $categoryQuery = !empty($group['organisers_id'])
                                            ? ('?group_organisers_id=' . $group['organisers_id'])
                                            : '';
                                        $categoryFaqId = optional($items->first())->id;

                                        $categoryIndex = $sortedCategoryKeys->search(fn ($k) => (string) $k === (string) $category);
                                        $canCategoryMoveUp = $categoryFaqId && $categoryIndex !== false && $categoryIndex > 0;
                                        $canCategoryMoveDown = $categoryFaqId && $categoryIndex !== false && $categoryIndex < ($sortedCategoryKeys->count() - 1);
                                    @endphp
                                    <div class="rounded border border-gray-200 shadow-sm p-3 my-2 bg-gray-50">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="font-bold text-lg text-gray-900">
                                                {{ $category }}
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                @if($canCategoryMoveUp)
                                                    <a class="btn btn-sm btn-outline-primary"
                                                       href="{{ route('faq.category.up', ['organiser' => $selectedOrganiserId, 'faq' => $categoryFaqId]) . $categoryQuery }}"
                                                       title="Kategorie hoch">
                                                        <box-icon name='up-arrow'></box-icon>
                                                    </a>
                                                @endif

                                                @if($canCategoryMoveDown)
                                                    <a class="btn btn-sm btn-outline-primary"
                                                       href="{{ route('faq.category.down', ['organiser' => $selectedOrganiserId, 'faq' => $categoryFaqId]) . $categoryQuery }}"
                                                       title="Kategorie runter">
                                                        <box-icon name='down-arrow'></box-icon>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            @php
                                                $sortedFaqs = $items->sortBy(['sort_order','id'])->values();
                                            @endphp
                                            @forelse($sortedFaqs as $faq)
                                                @php
                                                    $faqIndex = $sortedFaqs->search(fn ($f) => (int) $f->id === (int) $faq->id);
                                                    $canFaqMoveUp = $faqIndex !== false && $faqIndex > 0;
                                                    $canFaqMoveDown = $faqIndex !== false && $faqIndex < ($sortedFaqs->count() - 1);
                                                    $faqPanelId = $accordionId . '-faq-' . (int) $faq->id;
                                                @endphp
                                                <div class="rounded border border-gray-200 bg-white p-3 mb-2 shadow-sm" data-faq-item>
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="min-w-0 flex-1">
                                                            <div class="flex items-center gap-2">
                                                                @if((int) $faq->is_active !== 1)
                                                                    <span class="text-xs px-2 py-0.5 rounded bg-red-100 text-red-800">Inaktiv</span>
                                                                @endif
                                                            </div>

                                                            <button type="button"
                                                                    class="font-semibold break-words mt-1 text-gray-900 text-left w-full flex items-center justify-between gap-2"
                                                                    data-faq-trigger
                                                                    aria-expanded="false"
                                                                    aria-controls="{{ $faqPanelId }}">
                                                                <span>{{ $faq->question }}</span>
                                                                <span class="text-gray-500 text-sm select-none" aria-hidden="true">▾</span>
                                                            </button>

                                                            {{-- Antwort (initial hidden, wird per JS aufgeklappt) --}}
                                                            @if(!empty($faq->answer_html))
                                                                <div id="{{ $faqPanelId }}" class="mt-2 rounded bg-gray-100 border border-gray-200 p-3 text-gray-900" data-faq-panel hidden>
                                                                    <div class="prose prose-sm max-w-none text-gray-900">
                                                                        {!! $faq->answer_html !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="flex items-center gap-2 shrink-0">
                                                            @if($canFaqMoveUp)
                                                                <a class="btn btn-sm btn-outline-primary"
                                                                   href="{{ route('faq.up', ['organiser' => $selectedOrganiserId, 'faq' => $faq->id]) }}"
                                                                   title="Hoch">
                                                                    <box-icon name='chevron-up'></box-icon>
                                                                </a>
                                                            @endif

                                                            @if($canFaqMoveDown)
                                                                <a class="btn btn-sm btn-outline-primary"
                                                                   href="{{ route('faq.down', ['organiser' => $selectedOrganiserId, 'faq' => $faq->id]) }}"
                                                                   title="Runter">
                                                                    <box-icon name='chevron-down'></box-icon>
                                                                </a>
                                                            @endif

                                                            @if((int) $faq->is_active === 1)
                                                                <a class="btn btn-sm btn-outline-primary"
                                                                   href="{{ route('faq.inaktiv', ['organiser' => $selectedOrganiserId, 'faq' => $faq->id]) }}"
                                                                   title="Aktiv">
                                                                    <box-icon name='show'></box-icon>
                                                                </a>
                                                            @else
                                                                <a class="btn btn-sm btn-outline-primary"
                                                                   href="{{ route('faq.aktiv', ['organiser' => $selectedOrganiserId, 'faq' => $faq->id]) }}"
                                                                   title="Inaktiv">
                                                                    <box-icon name='hide'></box-icon>
                                                                </a>
                                                            @endif

                                                            <a class="btn btn-sm btn-outline-primary"
                                                               href="{{ route('faq.edit', ['organiser' => $selectedOrganiserId, 'faq' => $faq->id]) }}"
                                                               title="Bearbeiten">
                                                                <box-icon name='edit' type='solid'></box-icon>
                                                            </a>

                                                            <form class="inline"
                                                                  action="{{ route('faq.destroy', ['organiser' => $selectedOrganiserId, 'faq' => $faq->id]) }}"
                                                                  method="post"
                                                                  onsubmit="return confirm('FAQ wirklich löschen?\n\nKategorie: {{ addslashes($category) }}\nFrage: {{ addslashes($faq->question) }}');">
                                                                @csrf
                                                                <button class="btn btn-sm btn-outline-primary" type="submit" title="Löschen">
                                                                    <box-icon name='trash' type='solid'></box-icon>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-gray-500">Noch keine FAQs vorhanden.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-gray-500">Noch keine FAQs vorhanden.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <script>
                        // Backend-Accordion: initial alles zu, beim Öffnen genau eine Antwort sichtbar.
                        (function () {
                            var root = document.querySelector('[data-faq-accordion="{{ $accordionId }}"]');
                            if (!root) return;

                            function closeAll(exceptPanel) {
                                var items = root.querySelectorAll('[data-faq-item]');
                                items.forEach(function (item) {
                                    var trigger = item.querySelector('[data-faq-trigger]');
                                    var panel = item.querySelector('[data-faq-panel]');
                                    if (!trigger || !panel) return;
                                    if (exceptPanel && panel === exceptPanel) return;

                                    panel.hidden = true;
                                    trigger.setAttribute('aria-expanded', 'false');
                                });
                            }

                            closeAll();

                            root.addEventListener('click', function (e) {
                                var trigger = e.target && e.target.closest ? e.target.closest('[data-faq-trigger]') : null;
                                if (!trigger || !root.contains(trigger)) return;

                                var item = trigger.closest('[data-faq-item]');
                                if (!item) return;

                                var panel = item.querySelector('[data-faq-panel]');
                                if (!panel) return; // Kein Panel (z.B. leere Antwort)

                                var isOpen = !panel.hidden;
                                if (isOpen) {
                                    panel.hidden = true;
                                    trigger.setAttribute('aria-expanded', 'false');
                                } else {
                                    closeAll(panel);
                                    panel.hidden = false;
                                    trigger.setAttribute('aria-expanded', 'true');
                                }
                            });
                        })();
                    </script>
                </div>
            @empty
                <div class="m-2 text-gray-500">Noch keine FAQs vorhanden.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
