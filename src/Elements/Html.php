<?php

namespace Mods\Form\Elements;

class Html extends FormControl
{
    protected $name;

    protected $content;

    protected $template = '%s';

    protected $attributes = [
        'type' => 'html',
    ];

    public function render()
    {
        return sprintf($this->template, $this->content());
    }

    public function content($content = null)
    {
        if (is_null($content)) {
            return $this->content;
        }

        $this->content = $content;
        return $this;
    }
}
