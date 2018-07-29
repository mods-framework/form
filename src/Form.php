<?php

namespace Mods\Form;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;
use Mods\Form\Elements\Element;

abstract class Form
{
    /**
    * @var Request
    */
    protected $request;

    /**
    * @var Session
    */
    protected $session;

    /**
     * All fields that are added.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Form Settings.
     *
     * @var array
     */
    protected $settings = [];

    /**
     * The registered Form extenders.
     *
     * @var array
     */
    protected static $extendFields = [];

    /**
     * Intilize the form.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request, Session $session)
    {
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Get the form action.
     *
     * @return array
     */
    abstract public function open();

    /**
     * Get the fields for the form.
     *
     * @return array
     */
    abstract public function fields();

    /**
     * Get the form actions.
     *
     * @return array
     */
    abstract public function actions();

    /**
     * Register a callable for extending the form
     *
     * @return void
     */
    public static function extend(Closure $callable)
    {
        if (!isset(static::$extendFields[static::class])) {
            static::$extendFields[static::class] = [];
        }
        static::$extendFields[static::class][] = $callable;
    }

    /**
     * Render the form.
     *
     * @return array
     */
    public function build()
    {
        $this->buildForm();

        $this->extendForm();

        $this->readForm();

        return [
            'formFields' => $this->fields,
            'formOpen' => $this->open(),
            'formActions' => $this->actions()
        ];
    }

    /**
     * Retrive the validation rules that
     * are added to the form.
     *
     * @return array
     */
    public function rules()
    {
        $this->buildForm();

        $this->extendForm();
        
        $rules = [];

        foreach ($this->fields() as $field) {
            if (!is_null($field->getRules())) {
                $rules[$field->getName()] = $field->getRules();
            }
        }

        return $rules;
    }

    /**
     * Add a FormField to the form's fields.
     *
     * @param FormField $field
     * @return $this
     */
    public function addField(Element $field)
    {
        if ($field->getType() == 'radio') {
            $this->fields[$field->getId()] = $field;
            $field->group($field->getName());
        } else {
            $this->fields[$field->getName()] = $field;
        }
        return $this;
    }

    /**
     * Add the fileds related to the form.
     *
     * @return void
     */
    private function buildForm()
    {
        foreach ($this->fields() as $field) {
            $this->addField($field);
        }
    }

    /**
     * Extends the form for addtional fields
     *
     * @return void
     */
    private function extendForm()
    {
        if (!isset(static::$extendFields[static::class])) {
            return;
        }

        foreach (static::$extendFields[static::class] as $callable) {
            $callable($this);
        }
    }

    /**
     * Extends the form for addtional fields
     *
     * @return void
     */
    private function readForm()
    {
        foreach ($this->fields as $field) {
            $field->setForm($this);

            if ($field->hasError()) {
                $field->addClass('is-invalid');
            }


            if ($old = $this->request->old($field->getName())) {
                $field->value($old);
            }
        }
    }

    /**
     * Add field before another field.
     *
     * @param FormField $field
     * @return $this
     */
    public function addBefore($name, Element $field)
    {
        $offset = array_search($name, array_keys($this->fields));
        $beforeFields = array_slice($this->fields, 0, $offset);
        $afterFields = array_slice($this->fields, $offset);
        $this->fields = $beforeFields;
        $this->addField($field);
        $this->fields += $afterFields;
        return $this;
    }

    /**
     * Add field before another field.
     *
     * @param FormField $field
     * @return $this
     */
    public function addAfter($name, Element $field)
    {
        $offset = array_search($name, array_keys($this->fields));
        $beforeFields = array_slice($this->fields, 0, $offset + 1);
        $afterFields = array_slice($this->fields, $offset + 1);
        $this->fields = $beforeFields;
        $this->addField($field);
        $this->fields += $afterFields;
        return $this;
    }

    /**
     * Remove field with specified name from the form.
     *
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        if ($this->has($name)) {
            unset($this->fields[$name]);
        }
        return $this;
    }

    /**
     * Check if form has field.
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->fields);
    }

    /**
     * Add or Retrive Settings of the form.
     *
     * @param array|null $settings
     * @return array|Form
     */
    public function settings($settings = null)
    {
        if (is_null($settings)) {
            return $this->settings;
        }

        $this->settings = $settings;
        return $this;
    }

    /**
     * Get the field error.
     *
     * @param string $field
     * @return string
     */
    public function getError($field)
    {
        if (! $this->hasError($field)) {
            return null;
        }
        return $this->getErrors()->first($field);
    }

    /**
    * Check if the field has error.
    *
    * @param string $field
    * @return bool
    */
    public function hasError($field)
    {
        if (! $this->hasErrors()) {
            return false;
        }
        return $this->getErrors()->has($field);
    }

    /**
    * Check if session has error.
    *
    * @return bool
    */
    protected function hasErrors()
    {
        return $this->session->has('errors');
    }

    /**
     * Get the error.
     *
     * @return string
     */
    protected function getErrors()
    {
        return $this->hasErrors() ? $this->session->get('errors') : null;
    }
}
