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
     * @var \Illuminate\Support\Collection
     */
    public $languages;

    /**
     * Current language code (capitalized)
     *
     * @var string
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
    public function __construct($justLinks = false)
    {
        $this->justLinks = $justLinks;

        $this->languages = Language::whereStatus(1)
            ->select('id', 'name', 'code', 'emoji')
            ->get();

        // Read cookie safely and decode JSON if possible
        $cookie = Cookie::get('language_data');
        $cookieArray = [];
        if ($cookie && is_string($cookie)) {
            $decoded = json_decode($cookie, true);
            if (is_array($decoded)) {
                $cookieArray = $decoded;
            }
        }

        // Priority: cookie.code -> cookie.id -> cookie.emoji (map to code) -> app locale -> first language in DB
        if (!empty($cookieArray['code'])) {
            $this->currentLangData = strtoupper($cookieArray['code']);
        } elseif (!empty($cookieArray['id'])) {
            $found = $this->languages->firstWhere('id', $cookieArray['id']);
            $this->currentLangData = strtoupper($found->code ?? ($this->languages->first()->code ?? app()->getLocale()));
        } elseif (!empty($cookieArray['emoji'])) {
            $found = $this->languages->firstWhere('emoji', $cookieArray['emoji']);
            $this->currentLangData = strtoupper($found->code ?? $cookieArray['emoji']);
        } else {
            $locale = app()->getLocale();
            $found = $this->languages->firstWhere('code', $locale);
            $this->currentLangData = strtoupper($found->code ?? ($this->languages->first()->code ?? $locale));
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.flag-toggle');
    }
}
