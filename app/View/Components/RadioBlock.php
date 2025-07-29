<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RadioBlock extends Component
{

    /**
     * Id of the input box
     *
     * @var string
     */
    public $boxName;

    /**
     * Name of the label
     *
     * @var string
     */
    public $text;

    /**
     * 
     * Additional class name of Parent <div> tag
     * */
    public $parentDivClass;

    /**
     * Type of input
     * checkbox or radio
     * */
    public $boxType;

    /**
     * Radio button value
     * */
    public $value;

    /**
     * Id of the checkbox or radio
     * */
    public $id;

    /**
     * Radio or checkbox checked or not
     * @return boolean
     * */
    public $checked;
    
    /**
     * Create a new component instance.
     */
    public function __construct($text, $id, $boxType="checkbox",$checked = false, $value=null, $boxName = null, $parentDivClass = null)
    {
        $this->id = $id;
        $this->boxName = $boxName;
        $this->parentDivClass = $parentDivClass;
        $this->boxType = $boxType;
        $this->text = $text;
        $this->value = $value;
        $this->checked = $checked;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.radio-block');
    }
}
