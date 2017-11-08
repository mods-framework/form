<?php

namespace Mods\Form\Elements;

class TextArea extends FormControl
{
    protected $attributes = [
        'type' => 'textarea',
        'name' => '',
        'rows' => 10,
        'cols' => 50,
    ];

    protected $value;

    protected $template = '<textarea%s>%s</textarea>';

    public function render()
    {
        return sprintf($this->template, $this->renderAttributes(), $this->escape($this->value));
    }

    public function rows($rows)
    {
        $this->setAttribute('rows', $rows);

        return $this;
    }

    public function cols($cols)
    {
        $this->setAttribute('cols', $cols);

        return $this;
    }

    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->setAttribute('placeholder', $placeholder);

        return $this;
    }

    public function defaultValue($value)
    {
        if (! $this->hasValue()) {
            $this->value($value);
        }

        return $this;
    }

    protected function hasValue()
    {
        return isset($this->value);
    }
}
