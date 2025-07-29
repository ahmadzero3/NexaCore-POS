<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Language;
use Illuminate\Support\Facades\Cookie;

class FlagToggle extends Component
{   
    /**
     * Language array
     *
     * @var array
     */
    public $languages;

    /**
     * Language array
     *
     * @var array
     */
    public $currentLangData;

    /**
     * View Type
     *
     * @var boolean
     */
    public $justLinks;

    /**
     * Create a new component instance.
     */
    public function __construct($justLinks=false)
    {
        $this->justLinks = $justLinks;
        
        $this->languages = Language::whereStatus(1)
                            ->select('id','name','code','emoji')
                            ->get();
        /**
         * Encoded JSON
         * @array
         * */
        $cookie = Cookie::get('language_data');

        /**
         * Decode JSON
         * */
        $cookieArray = json_decode($cookie, true);

        // Set currentLangData based on language emoji or a default value
        $this->currentLangData = $cookieArray['emoji']??'';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.flag-toggle');
    }
}
