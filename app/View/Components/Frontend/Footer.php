<?php

namespace App\View\Components\Frontend;

use App\Models\Organiser;
use Illuminate\View\Component;

class Footer extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $organiser = Organiser::where('veranstaltungDomain', $_SERVER['HTTP_HOST'])->first();
        if ($organiser === null) {
            $organiser = Organiser::find(1);
        }

        return view('components.frontend.footer' , compact('organiser'));
    }
}
