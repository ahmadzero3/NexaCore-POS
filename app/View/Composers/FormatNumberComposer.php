<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Traits\FormatNumber;

class FormatNumberComposer
{
    public function compose(View $view)
    {
        $formatter = new class {
            use FormatNumber;
        };
        $view->with('formatNumber', $formatter);
    }
}