<!-- ======= FAQ Section ======= -->
@php
    // Erwartet: $groups = [ ['title' => '...', 'items' => [ ['question' => '...', 'answer_html' => '...'], ...]], ...]
    // Kommt i.d.R. aus dem Controller (DB).
    $groups = $groups ?? [];

    $accordionId = $accordionId ?? 'faq-accordion';
@endphp

<section id="faq" class="faq py-8" data-faq-accordion="{{ $accordionId }}">
    <div class="container mx-auto px-4">
        <div class="section-title" data-aos="fade-up">
            <h2>FAQ</h2>
            <p>Häufige Fragen</p>
        </div>

        @if(empty($groups))
            <div class="text-gray-700" data-aos="fade-up">
                <p>Aktuell sind noch keine FAQ-Einträge vorhanden.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($groups as $gIndex => $group)
                    @if(!empty($group['title']))
                        <h3 class="text-xl font-semibold" data-aos="fade-up" data-aos-delay="{{ min($gIndex * 50, 300) }}">{{ $group['title'] }}</h3>
                    @endif

                    <div class="space-y-4">
                        @foreach(($group['items'] ?? []) as $index => $item)
                            <details class="rounded border border-gray-200 bg-white p-4" data-faq-item data-aos="fade-up" data-aos-delay="{{ min(($gIndex * 50) + ($index * 25), 300) }}">
                                <summary class="cursor-pointer font-semibold text-lg">
                                    {{ $item['question'] ?? '' }}
                                </summary>

                                <div class="mt-2 text-gray-700">
                                    @if(!empty($item['answer_html']))
                                        {!! $item['answer_html'] !!}
                                    @else
                                        {{ $item['answer'] ?? '' }}
                                    @endif

                                    @if(!empty($item['links']) && is_array($item['links']))
                                        <div class="mt-2">
                                            @foreach($item['links'] as $link)
                                                @php
                                                    $label = $link['label'] ?? null;
                                                    $href = $link['href'] ?? null;
                                                    if (!$href && !empty($link['route'])) {
                                                        $href = route($link['route']);
                                                    }
                                                @endphp

                                                @if($label && $href)
                                                    <a class="underline mr-3" href="{{ $href }}">{{ $label }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </details>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        // Klassisches Accordion: nur ein <details> gleichzeitig offen.
        (function () {
            var root = document.querySelector('[data-faq-accordion="{{ $accordionId }}"]');
            if (!root) return;

            var items = root.querySelectorAll('details[data-faq-item]');
            if (!items || !items.length) return;

            items.forEach(function (d) {
                d.addEventListener('toggle', function () {
                    if (!d.open) return;
                    items.forEach(function (other) {
                        if (other !== d) other.open = false;
                    });
                });
            });
        })();
    </script>
</section><!-- End FAQ Section -->
