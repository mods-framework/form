<?php

namespace Mods\Form;

use Closure;
use Mods\Form\Elements\Element;

abstract class Form
{
	 /**
     * @var FormBuilder
     */
    protected $formBuilder;

	/**
     * All fields that are added.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Model to use.
     *
     * @var array
     */
    protected $model = [];

    /**
     * Model to use.
     *
     * @var mixed
     */
    protected $formOpen;

    /**
     * Model to use.
     *
     * @var string
     */
    protected $formClose;

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
     * @param FormBuilder $formBuilder
     * @return void
     */
    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }

    /**
     * Add the fileds related to the form.
     *
     * @return void
     */
	abstract protected function buildForm();

    /**
     * Render the form.
     *
     * @return array
     */
	public function build()
	{
		$this->buildForm();

        $this->extendForm();

        $form = [
            'formOpen' => $this->formOpen,
            'formFields' => $this->fields,
            'formClose' =>  $this->formClose
        ];

		return $form;
	}

    /**
     * Register a callable for extending the form
     *
     * @return void
     */
    public static function extend(Closure $callable)
    {
        static::$extendFields[] = $callable;
    }

    /**
     * Extends the form for addtional fields
     *
     * @return void
     */
    protected function extendForm() {
        foreach(static::$extendFields as $callable) {
           $callable($this);
        }
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

        foreach ($this->fields as $field) {
            if(!is_null($field->getRules())) {
                $rules[$field->getName()] = $field->getRules();
            }
        }     

        return $rules;
    }

    /**
     * Add or Retrive Settings of the form.
     *
     * @param array|null $settings
     * @return array|Form
     */
    public function settings($settings = null)
    {
        if(is_null($settings)) {
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
        return $this->formBuilder->getError($field);
    }

    /**
    * Check if the field has error.
    *  
    * @param string $field
    * @return bool
    */
    public function hasError($field)
    {
        return $this->formBuilder->hasError($field);
    }

    /**
     * Create Form open
     *
     * @param Closure $callable
     * @return Form
     */
    public function open(Closure $callable)
    {
        $open = $this->formBuilder->open();

        $callable($open);

        $this->formOpen = $open;

        return $this;
    }

    /**
     * Create Form close
     *
     * @param Closure $callable
     * @return Form
     */
    public function close()
    {
        $close = $this->formBuilder->close();

        $this->formClose = $close;

        return $this;
    }

	 /**
     * Create a new field and add it to the form.
     *
     * @param string $name
     * @param string $type
     * @param Closure $callable
     * @return $this
     */
    public function add($name, $type = 'text', Closure $callable)
    {
        $this->addField($this->makeField($name, $type, $callable));
        return $this;
    }

    /**
     * Add field before another field.
     *
     * @param string  $name         Name of the field before which new field is added.
     * @param string  $fieldName    Field name which will be added.
     * @param string  $type
     * @param Closure $callable
     * @return $this
     */
    public function addBefore($name, $fieldName, $type = 'text', Closure $callable)
    {
        $offset = array_search($name, array_keys($this->fields));
        $beforeFields = array_slice($this->fields, 0, $offset);
        $afterFields = array_slice($this->fields, $offset);
        $this->fields = $beforeFields;
        $this->add($fieldName, $type, $callable);
        $this->fields += $afterFields;
        return $this;
    }

    /**
     * Add field before another field.
     *
     * @param string  $name         Name of the field after which new field is added.
     * @param string  $fieldName    Field name which will be added.
     * @param string  $type
     * @param Closure $callable
     * @return $this
     */
    public function addAfter($name, $fieldName, $type = 'text', Closure $callable)
    {
        $offset = array_search($name, array_keys($this->fields));
        $beforeFields = array_slice($this->fields, 0, $offset + 1);
        $afterFields = array_slice($this->fields, $offset + 1);
        $this->fields = $beforeFields;
        $this->add($fieldName, $type, $callable);
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
     * Add a FormField to the form's fields.
     *
     * @param FormField $field
     * @return $this
     */
    protected function addField(Element $field)
    {
        if($field->getType() == 'radio') {
            $this->fields[$field->getId()] = $field;
             $field->group($field->getName());
        } else {
            $this->fields[$field->getName()] = $field;
        }
        return $this;
    }

    /**
     * Create the FormField object.
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     * @return FormField
     */
    protected function makeField($name, $type = 'text', Closure $callable)
    {   
        $field = call_user_func([$this->formBuilder, $type], $name);

        $callable($field);

        return $field;
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
}