<?php

namespace Bllim\Laravalid;

/*
 * This class is extending \Collective\Html\FormBuilder to make 
 * validation easy for both client and server side. Package convert 
 * laravel validation rules to javascript validation plugins while 
 * using laravel FormBuilder.
 *
 * USAGE: Just pass $rules to Form::open($options, $rules) and use.
 * You can also pass by using Form::setValidation from controller or router
 * for coming first form::open.
 * When Form::close() is used, $rules are reset.
 *
 * NOTE: If you use min, max, size, between and type of input is different from string
 * don't forget to specify the type (by using numeric, integer).
 *
 * @package    Laravel Validation For Client-Side
 * @author     Bilal Gultekin <bilal@bilal.im>
 * @license    MIT
 * @see        Collective\Html\FormBuilder
 * @version    0.9
 */

class FormBuilder extends \Collective\Html\FormBuilder
{
    protected $converter;

    public function __construct(\Collective\Html\HtmlBuilder $html, \Illuminate\Routing\UrlGenerator $url, $view, $csrfToken, Converter\Base\Converter $converter)
    {
        parent::__construct($html, $url, $view, $csrfToken);
        $plugin = \Config::get('laravalid.plugin');
        $this->converter = $converter;
    }

    /**
     * Set rules for validation.
     *
     * @param array $rules Laravel validation rules
     */
    public function setValidation($rules, $formName = null)
    {
        $this->converter()->set($rules, $formName);
    }

    /**
     * Get binded converter class.
     *
     * @param array $rules Laravel validation rules
     */
    public function converter()
    {
        return $this->converter;
    }

    /**
     * Reset validation rules.
     */
    public function resetValidation()
    {
        $this->converter()->reset();
    }

    /**
     * @param array $options
     * @param null $rules
     * @return \Illuminate\Support\HtmlString
     */
    public function open(array $options = [], $rules = null)
    {
        $this->setValidation($rules);

        if (isset($options['name'])) {
            $this->converter->setFormName($options['name']);
        } else {
            $this->converter->setFormName(null);
        }

        return parent::open($options);
    }

    /**
     * @param mixed $model
     * @param array $options
     * @param null $rules
     * @return \Illuminate\Support\HtmlString
     */
    public function model($model, array $options = [], $rules = null)
    {
        $this->setValidation($rules);

        return parent::model($model, $options);
    }

    /**
     * @param string $type
     * @param string $name
     * @param null $value
     * @param array $options
     * @return \Illuminate\Support\HtmlString
     */
    public function input($type, $name, $value = null, $options = [])
    {
        $options = $this->converter->convert(Helper::getFormAttribute($name)) + $options;

        return parent::input($type, $name, $value, $options);
    }

    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return \Illuminate\Support\HtmlString
     */
    public function textarea($name, $value = null, $options = [])
    {
        $options = $this->converter->convert(Helper::getFormAttribute($name)) + $options;

        return parent::textarea($name, $value, $options);
    }

    /**
     * @param string $name
     * @param array $list
     * @param null $selected
     * @param array $selectAttributes
     * @param array $optionsAttributes
     * @param array $optgroupsAttributes
     * @return \Illuminate\Support\HtmlString
     */
    public function select($name, $list = [], $selected = null, array $selectAttributes = [], array $optionsAttributes = [], array $optgroupsAttributes = [])
    {
        $optionsAttributes = $this->converter->convert(Helper::getFormAttribute($name)) + $optionsAttributes;
        $selectAttributes = $this->converter->convert(Helper::getFormAttribute($name)) + $selectAttributes;

        return parent::select($name, $list, $selected, $selectAttributes, $optionsAttributes, $optgroupsAttributes);
    }

    /**
     * @param string $type
     * @param string $name
     * @param mixed $value
     * @param bool $checked
     * @param array $options
     * @return \Illuminate\Support\HtmlString
     */
    protected function checkable($type, $name, $value, $checked, $options)
    {
        $options = $this->converter->convert(Helper::getFormAttribute($name)) + $options;

        return parent::checkable($type, $name, $value, $checked, $options);
    }

    /**
     * @return string
     */
    public function close()
    {
        $this->resetValidation();

        return parent::close();
    }
}
