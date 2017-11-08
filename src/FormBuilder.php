<?php

namespace Mods\Form;

use Mods\Form\Binding\BoundData;
use Mods\Form\Elements\Button;
use Mods\Form\Elements\Checkbox;
use Mods\Form\Elements\Date;
use Mods\Form\Elements\DateTimeLocal;
use Mods\Form\Elements\Email;
use Mods\Form\Elements\File;
use Mods\Form\Elements\FormOpen;
use Mods\Form\Elements\Hidden;
use Mods\Form\Elements\Label;
use Mods\Form\Elements\Password;
use Mods\Form\Elements\RadioButton;
use Mods\Form\Elements\Select;
use Mods\Form\Elements\Text;
use Mods\Form\Elements\TextArea;
use Mods\Form\Elements\MarkDown;
use Mods\Form\Elements\Html;
use Mods\Form\ErrorStore\ErrorStoreInterface;
use Mods\Form\OldInput\OldInputInterface;

class FormBuilder
{
    protected $oldInput;

    protected $errorStore;

    protected $boundData;


    public function __construct(OldInputInterface $oldInputProvider, ErrorStoreInterface $errorStore)
    {
        $this->oldInput = $oldInputProvider;
        $this->errorStore = $errorStore;
    }

    public function open()
    {
        $open = new FormOpen;
        return $open;
    }

    public function close()
    {
        $this->unbindData();

        return '</form>';
    }

    public function text($name)
    {
        $text = new Text($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $text->value($value);
        }

        return $text;
    }

    public function date($name)
    {
        $date = new Date($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $date->value($value);
        }

        return $date;
    }

    public function dateTimeLocal($name)
    {
        $date = new DateTimeLocal($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $date->value($value);
        }

        return $date;
    }

    public function email($name)
    {
        $email = new Email($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $email->value($value);
        }

        return $email;
    }

    public function hidden($name)
    {
        $hidden = new Hidden($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $hidden->value($value);
        }

        return $hidden;
    }

    public function textarea($name)
    {
        $textarea = new TextArea($name);

        if (!is_null($value = $this->getValueFor($name))) {
            $textarea->value($value);
        }

        return $textarea;
    }

    public function password($name)
    {
        return new Password($name);
    }

    public function checkbox($name, $value = 1)
    {
        $checkbox = new Checkbox($name, $value);

        $oldValue = $this->getValueFor($name);
        $checkbox->setOldValue($oldValue);

        return $checkbox;
    }

    public function radio($name, $value = null)
    {
        $radio = new RadioButton($name, $value);

        $oldValue = $this->getValueFor($name);
        $radio->setOldValue($oldValue);

        return $radio;
    }

    public function button($name)
    {
        return new Button($name);
    }

    public function reset($name)
    {
        $reset = new Button($name);
        $reset->attribute('type', 'reset');

        return $reset;
    }

    public function submit($name)
    {
        $submit = new Button($name);
        $submit->attribute('type', 'submit');

        return $submit;
    }

    public function select($name, $options = [])
    {
        $select = new Select($name, $options);

        $selected = $this->getValueFor($name);
        $select->select($selected);

        return $select;
    }

    public function label($label)
    {
        return new Label($label);
    }

    public function file($name)
    {
        return new File($name);
    }

    public function markdown($name)
    {
        return new MarkDown($name);
    }

    public function html($name)
    {
        return new Html($name);
    }

    public function hasError($name)
    {
        if (! isset($this->errorStore)) {
            return false;
        }

        return $this->errorStore->hasError($name);
    }

    public function getError($name, $format = null)
    {
        if (! isset($this->errorStore)) {
            return null;
        }

        if (! $this->hasError($name)) {
            return '';
        }

        $message = $this->errorStore->getError($name);

        if ($format) {
            $message = str_replace(':message', $message, $format);
        }

        return $message;
    }

    public function bind($data)
    {
        $this->boundData = new BoundData($data);
    }

    public function getValueFor($name)
    {
        if ($this->hasOldInput()) {
            return $this->getOldInput($name);
        }

        if ($this->hasBoundData()) {
            return $this->getBoundValue($name, null);
        }

        return null;
    }

    protected function hasOldInput()
    {
        if (! isset($this->oldInput)) {
            return false;
        }

        return $this->oldInput->hasOldInput();
    }

    protected function getOldInput($name)
    {
        return $this->oldInput->getOldInput($name);
    }

    protected function hasBoundData()
    {
        return isset($this->boundData);
    }

    protected function getBoundValue($name, $default)
    {
        return $this->boundData->get($name, $default);
    }

    protected function unbindData()
    {
        $this->boundData = null;
    }

    public function selectMonth($name)
    {
        $options = [
            "1" => "January",
            "2" => "February",
            "3" => "March",
            "4" => "April",
            "5" => "May",
            "6" => "June",
            "7" => "July",
            "8" => "August",
            "9" => "September",
            "10" => "October",
            "11" => "November",
            "12" => "December",
        ];

        return $this->select($name, $options);
    }
}
