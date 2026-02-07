<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Action extends Component
{
    public $row, $print, $edit, $delete, $permanentdelete, $restore, $detail, $custom, $information=false;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $row,
        $print,
        $edit,
        $delete,
        $permanentdelete,
        $restore,
        $detail,
        $custom = '',
        $information = true
    ) {
        //
        $this->row = $row;
        $this->print = $print;
        $this->edit = $edit;
        $this->delete = $delete;
        $this->detail = $detail;
        $this->permanentdelete = $permanentdelete;
        $this->restore = $restore;
        $this->custom = $custom;
        $this->information = $information;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.action');
    }
}
