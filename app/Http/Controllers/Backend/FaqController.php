<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Organiser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request, Organiser $organiser): View
    {
        // Der ausgewählte Organisator kommt jetzt über die Route: /backend/FAQ/{organiser}
        $organiserId = $organiser?->id;

        $faqs = Faq::query()
            ->where('use_organisers', true)
            ->orderByRaw('CASE WHEN organisers_id IS NULL THEN 0 WHEN organisers_id = ? THEN 1 ELSE 2 END', [$organiserId])
            ->orderBy('organisers_id')
            ->orderBy('category_sort_order')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $organiserNames = Organiser::query()
            ->select(['id', 'veranstaltung'])
            ->pluck('veranstaltung', 'id');

        // 1) Allgemein (NULL) + eigener Organisator
        $general = $faqs->filter(function (Faq $faq) use ($organiserId) {
            if (is_null($faq->organisers_id)) {
                return true;
            }

            if ($organiserId === null) {
                return false;
            }

            return (int) $faq->organisers_id === (int) $organiserId;
        });

        // 2) Andere Organisatoren: gruppieren nach organisers_id
        $othersByOrganiser = $faqs
            ->filter(function (Faq $faq) use ($organiserId) {
                if (is_null($faq->organisers_id)) {
                    return false;
                }

                if ($organiserId === null) {
                    return true;
                }

                return (int) $faq->organisers_id !== (int) $organiserId;
            })
            ->groupBy('organisers_id');

        $faqGroups = collect();

        $generalByCategory = $general->groupBy('category');
        if ($generalByCategory->count() > 0) {
            $currentName = $organiserId ? ($organiserNames->get($organiserId) ?? ('Organisator #' . $organiserId)) : null;
            $generalTitle = $currentName
                ? ('Allgemein (inkl. ' . $currentName . ')')
                : 'Allgemein';

            $faqGroups->push([
                'title' => $generalTitle,
                'organisers_id' => null,
                'faqs' => $generalByCategory,
            ]);
        }

        $othersByOrganiser->keys()->sort()->each(function ($otherOrganiserId) use (&$faqGroups, $othersByOrganiser, $organiserNames) {
            $items = ($othersByOrganiser->get($otherOrganiserId) ?? collect());
            $byCategory = $items->groupBy('category');

            if ($byCategory->count() === 0) {
                return;
            }

            $name = $organiserNames->get($otherOrganiserId) ?? ('Organisator #' . $otherOrganiserId);

            $faqGroups->push([
                'title' => 'Organisator: ' . $name,
                'organisers_id' => (int) $otherOrganiserId,
                'faqs' => $byCategory,
            ]);
        });

        $selectedOrganiserId = $organiserId;

        return view('components.backend.faq.index', compact('faqGroups', 'organiser', 'selectedOrganiserId'));
    }

    public function create(Organiser $organiser): View
    {
        $categories = Faq::query()
            ->where('use_organisers', true)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('components.backend.faq.create', [
            'categories' => $categories,
            'organiser' => $organiser,
            'selectedOrganiserId' => $organiser->id,
        ]);
    }

    public function store(Request $request, Organiser $organiser): RedirectResponse
    {
        $data = $request->validate([
            'category'       => ['required', 'string', 'max:100'],
            'question'       => ['required', 'string'],
            'answer_html'    => ['required', 'string'],
            'is_active'      => ['nullable'],
            'only_organiser' => ['nullable'],
        ]);

        $category = trim($data['category']);

        $categorySortOrder = (int) (Faq::query()
            ->where('use_organisers', true)
            ->where(function ($q) use ($organiser) {
                $q->whereNull('organisers_id')->orWhere('organisers_id', $organiser->id);
            })
            ->where('category', $category)
            ->max('category_sort_order') ?? 0);

        // Wenn es die Kategorie in dieser Gruppe noch nicht gibt, setzen wir sie ans Ende.
        // Falls sie existiert, behalten wir die bestehende Sortierung bei.
        $categoryExists = Faq::query()
            ->where('use_organisers', true)
            ->where(function ($q) use ($organiser) {
                $q->whereNull('organisers_id')->orWhere('organisers_id', $organiser->id);
            })
            ->where('category', $category)
            ->exists();

        if (!$categoryExists) {
            $categorySortOrder = (int) (Faq::query()
                ->where('use_organisers', true)
                ->where(function ($q) use ($organiser) {
                    $q->whereNull('organisers_id')->orWhere('organisers_id', $organiser->id);
                })
                ->max('category_sort_order') ?? 0) + 1;
        }

        $nextSortOrder = (int) (Faq::query()
            ->where('use_organisers', true)
            ->where(function ($q) use ($organiser) {
                $q->whereNull('organisers_id')->orWhere('organisers_id', $organiser->id);
            })
            ->where('category', $category)
            ->max('sort_order') ?? 0) + 1;

        $onlyOrganiser = (bool) ($data['only_organiser'] ?? false);

        Faq::create([
            'category'            => $category,
            'category_sort_order' => $categorySortOrder,
            'question'            => $data['question'],
            'answer_html'         => $data['answer_html'],
            'sort_order'          => $nextSortOrder,
            'is_active'           => !empty($data['is_active']),
            'use_organisers'      => true,
            'organisers_id'       => $onlyOrganiser ? $organiser->id : null,
            'eventGroup_id'       => null,
            'event_id'            => null,
        ]);

        Session::flash('success', 'FAQ wurde angelegt.');

        return redirect()->route('faq.index', ['organiser' => $organiser->id]);
    }

    public function edit(Organiser $organiser, Faq $faq): View
    {
        // Sichtbarkeit: im Backend darf man innerhalb der Listen auch andere Organiser sehen.
        // Edit erlauben wir nur für global/eigen oder exakt den Fremd-Organiser.
        $faq = Faq::query()
            ->whereKey($faq->getKey())
            ->where('use_organisers', true)
            ->firstOrFail();

        $categories = Faq::query()
            ->where('use_organisers', true)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('components.backend.faq.edit', [
            'faq' => $faq,
            'categories' => $categories,
            'organiser' => $organiser,
            'selectedOrganiserId' => $organiser->id,
        ]);
    }

    public function update(Request $request, Organiser $organiser, Faq $faq): RedirectResponse
    {
        $faq = Faq::query()
            ->whereKey($faq->getKey())
            ->where('use_organisers', true)
            ->firstOrFail();

        $data = $request->validate([
            'category'       => ['required', 'string', 'max:100'],
            'question'       => ['required', 'string'],
            'answer_html'    => ['required', 'string'],
            'is_active'      => ['nullable'],
            'only_organiser' => ['nullable'],
        ]);

        $onlyOrganiser = (bool) ($data['only_organiser'] ?? false);

        $newCategory = trim($data['category']);
        $oldCategory = (string) $faq->category;

        // Ziel-Gruppe für organiser-spezifische FAQs (die Kategorie-Reihenfolge soll in der Zielgruppe konsistent sein)
        $targetOrganisersId = $onlyOrganiser ? $organiser->id : null;

        $categorySortOrder = $faq->category_sort_order;
        $sortOrder = $faq->sort_order;

        if ($newCategory !== $oldCategory || (int) $faq->organisers_id !== (int) $targetOrganisersId) {
            // Wenn Kategorie oder Gruppe geändert wurde, einsortieren ans Ende der Ziel-Kategorie.
            $categoryExists = Faq::query()
                ->where('use_organisers', true)
                ->where('category', $newCategory)
                ->where(function ($q) use ($targetOrganisersId, $organiser) {
                    // Für die Backend-Logik behandeln wir die "Allgemein"-Gruppe als (NULL + eigener). Beim Speichern bleibt organisers_id jedoch NULL oder organiser->id.
                    if ($targetOrganisersId === null) {
                        $q->whereNull('organisers_id');
                    } else {
                        $q->where('organisers_id', $targetOrganisersId);
                    }
                })
                ->exists();

            if (!$categoryExists) {
                $categorySortOrder = (int) (Faq::query()
                    ->where('use_organisers', true)
                    ->where(function ($q) use ($targetOrganisersId) {
                        if ($targetOrganisersId === null) {
                            $q->whereNull('organisers_id');
                        } else {
                            $q->where('organisers_id', $targetOrganisersId);
                        }
                    })
                    ->max('category_sort_order') ?? 0) + 1;
            } else {
                $categorySortOrder = (int) (Faq::query()
                    ->where('use_organisers', true)
                    ->where('category', $newCategory)
                    ->where(function ($q) use ($targetOrganisersId) {
                        if ($targetOrganisersId === null) {
                            $q->whereNull('organisers_id');
                        } else {
                            $q->where('organisers_id', $targetOrganisersId);
                        }
                    })
                    ->max('category_sort_order') ?? 0);
            }

            $sortOrder = (int) (Faq::query()
                ->where('use_organisers', true)
                ->where('category', $newCategory)
                ->where(function ($q) use ($targetOrganisersId) {
                    if ($targetOrganisersId === null) {
                        $q->whereNull('organisers_id');
                    } else {
                        $q->where('organisers_id', $targetOrganisersId);
                    }
                })
                ->max('sort_order') ?? 0) + 1;
        }

        $faq->update([
            'category'            => $newCategory,
            'category_sort_order' => $categorySortOrder,
            'sort_order'          => $sortOrder,
            'question'            => $data['question'],
            'answer_html'         => $data['answer_html'],
            'is_active'           => !empty($data['is_active']),
            'use_organisers'      => true,
            'organisers_id'       => $targetOrganisersId,
            'eventGroup_id'       => null,
            'event_id'            => null,
        ]);

        Session::flash('success', 'FAQ wurde gespeichert.');

        return redirect()->route('faq.index', ['organiser' => $organiser->id]);
    }

    public function destroy(Organiser $organiser, Faq $faq): RedirectResponse
    {
        $faq = Faq::query()
            ->whereKey($faq->getKey())
            ->where('use_organisers', true)
            ->firstOrFail();

        $faq->delete();

        Session::flash('success', 'FAQ wurde gelöscht.');

        return redirect()->route('faq.index', ['organiser' => $organiser->id]);
    }

    public function up(Organiser $organiser, Faq $faq): RedirectResponse
    {
        return $this->moveFaq($organiser, $faq, -1);
    }

    public function down(Organiser $organiser, Faq $faq): RedirectResponse
    {
        return $this->moveFaq($organiser, $faq, +1);
    }

    public function categoryUp(Organiser $organiser, Faq $faq): RedirectResponse
    {
        return $this->moveCategoryForFaq($organiser, $faq, -1);
    }

    public function categoryDown(Organiser $organiser, Faq $faq): RedirectResponse
    {
        return $this->moveCategoryForFaq($organiser, $faq, +1);
    }

    private function groupScopeForFaq(Organiser $organiser, Faq $faq): array
    {
        // Allgemein-Gruppe umfasst: NULL + eigener Organisator.
        // Andere Organisatoren: exakt organisers_id des Datensatzes.
        if (is_null($faq->organisers_id) || (int) $faq->organisers_id === (int) $organiser->id) {
            return ['type' => 'general', 'organisers_id' => null];
        }

        return ['type' => 'other', 'organisers_id' => (int) $faq->organisers_id];
    }

    private function moveFaq(Organiser $organiser, Faq $faq, int $direction): RedirectResponse
    {
        /** @var Faq $faq */
        $faq = Faq::query()
            ->whereKey($faq->getKey())
            ->where('use_organisers', true)
            ->firstOrFail();

        $scope = $this->groupScopeForFaq($organiser, $faq);

        $siblingsQuery = Faq::query()
            ->where('use_organisers', true)
            ->where('category', $faq->category);

        if ($scope['type'] === 'general') {
            $siblingsQuery->where(function ($q) use ($organiser) {
                $q->whereNull('organisers_id')
                  ->orWhere('organisers_id', $organiser->id);
            });
        } else {
            $siblingsQuery->where('organisers_id', $scope['organisers_id']);
        }

        $siblings = $siblingsQuery
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values();

        $index = $siblings->search(fn ($f) => (int) $f->id === (int) $faq->id);
        if ($index === false) {
            return redirect()->route('faq.index', ['organiser' => $organiser->id]);
        }

        $swapIndex = $index + ($direction < 0 ? -1 : 1);
        if ($swapIndex < 0 || $swapIndex >= $siblings->count()) {
            return redirect()->route('faq.index', ['organiser' => $organiser->id]);
        }

        $a = $siblings[$index];
        $b = $siblings[$swapIndex];

        $tmp = $a->sort_order;
        $a->sort_order = $b->sort_order;
        $b->sort_order = $tmp;

        $a->save();
        $b->save();

        return redirect()->route('faq.index', ['organiser' => $organiser->id]);
    }

    private function moveCategory(Organiser $organiser, string $category, int $direction): RedirectResponse
    {
        // Gruppe bestimmen: general (NULL + eigener) oder other (exakt organisers_id)
        // Der View hängt bei anderen Organisatoren ?group_organisers_id=<id> an.
        $groupOrganiserId = request()->query('group_organisers_id');
        $groupOrganiserId = ($groupOrganiserId === null || $groupOrganiserId === '') ? null : (int) $groupOrganiserId;

        $categoriesQuery = Faq::query()
            ->where('use_organisers', true)
            ->select('category', 'category_sort_order');

        if ($groupOrganiserId === null) {
            // Allgemein + eigener Organiser
            $categoriesQuery->where(function ($q) use ($organiser) {
                $q->whereNull('organisers_id')
                  ->orWhere('organisers_id', $organiser->id);
            });
        } else {
            // Exakt dieser fremde Organiser
            $categoriesQuery->where('organisers_id', $groupOrganiserId);
        }

        $categories = $categoriesQuery
            ->groupBy('category', 'category_sort_order')
            ->orderBy('category_sort_order')
            ->orderBy('category')
            ->get()
            ->values();

        $index = $categories->search(fn ($row) => (string) $row->category === (string) $category);
        if ($index === false) {
            return redirect()->route('faq.index', ['organiser' => $organiser->id]);
        }

        $swapIndex = $index + ($direction < 0 ? -1 : 1);
        if ($swapIndex < 0 || $swapIndex >= $categories->count()) {
            return redirect()->route('faq.index', ['organiser' => $organiser->id]);
        }

        $a = $categories[$index];
        $b = $categories[$swapIndex];

        $orderA = (int) $a->category_sort_order;
        $orderB = (int) $b->category_sort_order;

        $updateQueryA = Faq::query()->where('use_organisers', true)->where('category', $a->category);
        $updateQueryB = Faq::query()->where('use_organisers', true)->where('category', $b->category);

        if ($groupOrganiserId === null) {
            $updateQueryA->where(function ($q) use ($organiser) {
                $q->whereNull('organisers_id')->orWhere('organisers_id', $organiser->id);
            });
            $updateQueryB->where(function ($q) use ($organiser) {
                $q->whereNull('organisers_id')->orWhere('organisers_id', $organiser->id);
            });
        } else {
            $updateQueryA->where('organisers_id', $groupOrganiserId);
            $updateQueryB->where('organisers_id', $groupOrganiserId);
        }

        $updateQueryA->update(['category_sort_order' => $orderB]);
        $updateQueryB->update(['category_sort_order' => $orderA]);

        return redirect()->route('faq.index', ['organiser' => $organiser->id]);
    }

    private function moveCategoryForFaq(Organiser $organiser, Faq $faq, int $direction): RedirectResponse
    {
        /** @var Faq $faq */
        $faq = Faq::query()
            ->whereKey($faq->getKey())
            ->where('use_organisers', true)
            ->firstOrFail();

        return $this->moveCategory($organiser, (string) $faq->category, $direction);
    }

    public function aktiv(Organiser $organiser, Faq $faq): RedirectResponse
    {
        $faq = Faq::query()
            ->whereKey($faq->getKey())
            ->where('use_organisers', true)
            ->firstOrFail();

        $faq->update(['is_active' => true]);

        Session::flash('success', 'FAQ wurde aktiviert.');

        return redirect()->route('faq.index', ['organiser' => $organiser->id]);
    }

    public function inaktiv(Organiser $organiser, Faq $faq): RedirectResponse
    {
        $faq = Faq::query()
            ->whereKey($faq->getKey())
            ->where('use_organisers', true)
            ->firstOrFail();

        $faq->update(['is_active' => false]);

        Session::flash('success', 'FAQ wurde deaktiviert.');

        return redirect()->route('faq.index', ['organiser' => $organiser->id]);
    }
}
