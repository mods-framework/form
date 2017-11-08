<?php

namespace Mods\Form\Elements;

abstract class Input extends FormControl
{
    protected $template = '<input%s>';

    public function render()
    {
        return sprintf($this->template, $this->renderAttributes());
    }

    public function value($value)
    {
        $this->setValue($value);

        return $this;
    }

    protected function setValue($value)
    {
        $this->setAttribute('value', $value);

        return $this;
    }
}
