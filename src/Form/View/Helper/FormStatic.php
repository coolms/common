<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper;

use DateTime,
    Traversable,
    Zend\Form\ElementInterface,
    Zend\Form\FieldsetInterface,
    Zend\Form\View\Helper\FormInput,
    CmsCommon\View\Helper\HtmlContainer;

class FormStatic extends FormInput
{
    /**
     * Instance map to view helper
     *
     * @var array
     */
    protected $classMap = [
        DateTime::class     => 'typeDateTime',
        Traversable::class  => 'typeArrayOrTraversable',
    ];

    /**
     * Type map to view helper
     *
     * @var array
     */
    protected $typeMap = [
        'array'     => 'typeArrayOrTraversable',
        'float'     => 'typeNumeric',
        'int'       => 'typeNumeric',
        'integer'   => 'typeNumeric',
        'null'      => 'typeString',
        'object'    => 'typeObject',
        'string'    => 'typeString',
    ];

    /**
     * @var bool
     */
    private $shouldWrap = true;

    /**
     * {@inheritDoc}
     */
    public function render(ElementInterface $element)
    {
        if ($element->getAttribute('type') === 'hidden') {
            return '';
        }

        $value = $this->renderInstance($element);
        if ($value === null) {
            $value = $this->renderType($element);
        }

        if ($this->shouldWrap) {
            $attributes = $this->createAttributesString($element->getAttributes());
            return sprintf('<p %s>%s</p>', $attributes, $value);
        }

        return $value;
    }

    /**
     * Add form element type to plugin map
     *
     * @param string $type
     * @param string $plugin
     * @return self
     */
    public function addType($type, $plugin)
    {
        $this->typeMap[$type] = $plugin;

        return $this;
    }

    /**
     * Add instance class to plugin map
     *
     * @param string $class
     * @param string $plugin
     * @return self
     */
    public function addClass($class, $plugin)
    {
        $classMap = array_reverse($this->classMap, true);
        $classMap[$class] = $plugin;
        $this->classMap = array_reverse($classMap, true);

        return $this;
    }

    /**
     * Render value by helper name
     *
     * @param string $name
     * @param mixed $value
     * @param ElementInterface $element
     * @return string
     */
    protected function renderHelper($name, $value, ElementInterface $element)
    {
        $helper = $this->getView()->plugin($name);
        if ($helper instanceof HtmlContainer) {
            $this->shouldWrap = false;
            return $helper($value, $element->getAttributes());
        }

        $this->shouldWrap = true;
        return $helper($value);
    }

    /**
     * Render element by instance map
     *
     * @param ElementInterface $element
     * @return string|null
     */
    protected function renderInstance(ElementInterface $element)
    {
        $value = $this->getElementValue($element);

        foreach ($this->classMap as $class => $pluginName) {
            if ($value instanceof $class) {
                return $this->renderHelper($pluginName, $value, $element);
            }
        }

        return;
    }

    /**
     * Render element by type map
     *
     * @param ElementInterface $element
     * @return string|null
     */
    protected function renderType(ElementInterface $element)
    {
        $value = $this->getElementValue($element);
        $type  = gettype($value);

        if (isset($this->typeMap[$type])) {
            return $this->renderHelper($this->typeMap[$type], $value, $element);
        }

        return;
    }

    /**
     * @param ElementInterface $element
     * @return mixed
     */
    protected function getElementValue(ElementInterface $element)
    {
        return $element instanceof FieldsetInterface
            ? $element->getObject()
            : $element->getValue();
    }
}
