<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Organiser;

class FaqController extends Controller
{
    public function index()
    {
        $organiser = $this->currentOrganiser();

        $faqs = Faq::query()
            ->active()
            ->visibleForOrganiser($organiser?->id)
            ->orderBy('category_sort_order')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $groups = $faqs
            ->groupBy('category')
            ->map(function ($items, $category) {
                return [
                    'title' => $category,
                    'items' => $items->map(function (Faq $faq) {
                        return [
                            'question' => $faq->question,
                            'answer_html' => $faq->answer_html,
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();

        return view('pages.frontend.faq', compact('organiser', 'groups'));
    }

    private function currentOrganiser(): ?Organiser
    {
        $host = $_SERVER['HTTP_HOST'] ?? null;
        if (!$host) {
            return Organiser::find(1);
        }

        $organiser = Organiser::where('veranstaltungDomain', $host)->first();

        return $organiser ?: Organiser::find(1);
    }
}
