<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Traits\FormatsDateInputs;

class FormatDateComposer
{
    public function compose(View $view)
    {
        $formatter = new class {
            use FormatsDateInputs;
        };
        $view->with('formatDate', $formatter);
    }
}