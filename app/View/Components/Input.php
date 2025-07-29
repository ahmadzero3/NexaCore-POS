<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Name of the input box
     *
     * @var string
     */
    public $placeholder;

    /**
     * The inpux box type
     *
     * @var string
     */
    public $type;

    /**
     * Attribute name
     *
     * @var string
     */
    public $name;

    /**
     * Id of the input box
     *
     * @var string
     */
    public $id;

    /**
     * Attribute name
     *
     * @var string
     */
    public $value;

    /**
     * Attribute name
     *
     * @var string
     */
    public $required;

    /**
     * Attribute name
     *
     * @var boolean
     */
    public $autofocus;

    /**
     * Attribute name
     *
     * @var string
     */
    public $additionalClasses;

    /**
     * Selected option
     *
     * @var boolean
     */
    public $disabled;

    /**
     * Selected option
     *
     * @var boolean
     */
    public $readonly;

    public $autocomplete;

    public $minlength;
    public $maxlength;

    /**
     * Create a new component instance.
     */
    public function __construct($type,
                                $required = false,
                                $placeholder=null,
                                $name=null,
                                $id=null,
                                $value=null,
                                $additionalClasses=null,
                                $autofocus=false,
                                $disabled=false,
                                $readonly=false,
                                $autocomplete=false,
                                $minlength=null,
                                $maxlength=null,
                                )
    {
        $this->placeholder = $placeholder;
        $this->type = $type;
        $this->name = $name;
        $this->id = $id;
        $this->value = $value;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->readonly = $readonly;
        $this->additionalClasses = $additionalClasses;
        $this->autocomplete = $autocomplete;
        $this->minlength = $minlength;
        $this->maxlength = $maxlength;
        $this->autofocus = ($autofocus) ? "autofocus" : '';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.input');
    }
}
