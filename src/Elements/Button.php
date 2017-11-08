<?php

namespace Mods\Form\Elements;

class Button extends FormControl
{
    protected $attributes = [
        'type' => 'button',
    ];

    protected $template = '<button%s>%s</button>';

    protected $value;

    public function render()
    {
        return sprintf($this->template, $this->renderAttributes(), $this->value);
    }

    public function value($value)
    {
        $this->value = $value;

        return $this;
    }
}
