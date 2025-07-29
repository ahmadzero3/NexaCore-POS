<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    /**
     * Show the main page with dynamic customizations.
     */
    public function index()
    {
        // Grab only the keys we need, as [ key => value ]
        $custom = DB::table('customizations')
            ->whereIn('key', [
                'card_header_color',
                'card_border_color',
                'heading_color',
            ])
            ->pluck('value', 'key')
            ->all();

        // Pass to your Blade view
        return view('pages.index', compact('custom'));
    }
}
