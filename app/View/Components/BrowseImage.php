<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BrowseImage extends Component
{
    /**
     * Image Source URL
     * 
     * @var string
     */
    public $src;

    /**
     * Attribute name
     * 
     * @var string
     */
    public $name;

    /**
     * Attribute name
     * 
     * @var string
     */
    public $imageid;

    /**
     * Attribute name
     * 
     * @var string
     */
    public $inputBoxClass;

    /**
     * Attribute name
     * 
     * @var string
     */
    public $imageResetClass;

    /**
     * Create a new component instance.
     */
    public function __construct($src, $name, $imageid=null, $inputBoxClass=null, $imageResetClass)
    {
        $this->src = $src;
        $this->name = $name;
        $this->imageid = $imageid;
        $this->inputBoxClass = $inputBoxClass;
        $this->imageResetClass = $imageResetClass;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.browse-image');
    }
}
