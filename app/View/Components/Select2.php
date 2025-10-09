<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select2 extends Component
{
    public $index, $key, $label, $jenis, $subtext, $data;
    /**
     * Create a new component instance.
     */
    public function __construct($index, $key, $label, $jenis, $subtext, $data = [])
    {
        $this->index = $index;
        $this->key = $key;
        $this->label = $label;
        $this->jenis = $jenis;
        $this->subtext = $subtext;
        $this->data = $data;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select2', [
            'data' => $this->data,
        ]);
    }
}
