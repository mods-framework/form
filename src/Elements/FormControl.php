<?php

namespace Mods\Form\Elements;

use Mods\Form\Form;

abstract class FormControl extends Element
{
    protected $form;

    protected $label;

    protected $validationRules;

    protected $group;

    protected $template;

    public function __construct($name = null)
    {
        if ($name !== null) {
            $this->setName($name);
        }
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function group($group)
    {
        $this->group = $group;

        return $this;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function label($label)
    {
        $label = new Label($label);
        $label->forId(($this->getId())?:$this->getName());

        $this->label = $label;

        return $this;
    }

    public function getLabel()
    {
        if (is_null($this->label)) {
            return '';
        }
        return $this->label;
    }

    public function rules($rules)
    {
        $this->validationRules = $rules;

        return $this;
    }

    public function getRules()
    {
        return $this->validationRules;
    }

    public function setName($name)
    {
        $this->setAttribute('name', $name);

        return $this;
    }

    public function getName()
    {
        return $this->getAttribute('name');
    }

    public function getType()
    {
        return $this->getAttribute('type');
    }

    /**
     * Prepends HTML to the Input.
     *
     * @param  string $html
     * @return FormControl
     */
    public function prepend($html)
    {
        $this->template = $html.' '.$this->template;
        return $this;
    }

    /**
     * Appends HTML to the item.
     *
     * @param  string $html
     * @return FormControl
     */
    public function append($html)
    {
        $this->template = $this->template.' '.$html;
        return $this;
    }

    public function required()
    {
        $this->setAttribute('required');

        return $this;
    }

    public function optional()
    {
        $this->removeAttribute('required');

        return $this;
    }

    public function disable()
    {
        $this->setAttribute('disabled');

        return $this;
    }

    public function readonly()
    {
        $this->setAttribute('readonly');

        return $this;
    }

    public function enable()
    {
        $this->removeAttribute('disabled');
        $this->removeAttribute('readonly');

        return $this;
    }

    public function autofocus()
    {
        $this->setAttribute('autofocus');

        return $this;
    }

    public function unfocus()
    {
        $this->removeAttribute('autofocus');

        return $this;
    }

    /**
    * Check if the field has error.
    *
    * @param string $field
    * @return bool
    */
    public function hasError()
    {
        return $this->form->hasError($this->getName());
    }

    /**
    * Get the field error.
    *
    * @param string $field
    * @return string
    */
    public function getError()
    {
        return $this->form->getError($this->getName());
    }
}
