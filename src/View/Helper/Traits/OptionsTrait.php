<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper\Traits;

/**
 * Base functionality for all form view helpers
 */
trait OptionsTrait
{
    /**
     * @param string $attrib
     * @param mixed $value
     * @param ElementInterface $element
     * @return self
     */
    public function setAttribute($attrib, $value, ElementInterface $element = null)
    {
        $attribs = $element ? $this->getElementAttributes($element) : $this->getAttributes();
        $attribs[$attrib] = $value;
        $this->setAttributes($attribs, $element);

        return $this;
    }

    /**
     * @param string $attrib
     * @param ElementInterface $element
     * @return mixed
     */
    public function getAttribute($attrib, ElementInterface $element = null)
    {
        $attribs = $this->getAttributes($element);
        if (isset($attribs[$attrib])) {
            return $attribs[$attrib];
        }
    }

    /**
     * @param array|\Traversable $attribs
     * @param ElementInterface $element
     * @return self
     */
    public function setAttributes(array $attribs, ElementInterface $element = null)
    {
        if ($element) {
            empty($this->elementNamespace)
                ? $element->setAttributes($attribs)
                : $element->setAttribute($this->elementNamespace, $attribs);
        } elseif (property_exists($this, 'attributes')) {
            $this->attributes = $attribs;
        }

        return $this;
    }

    /**
     * @param ElementInterface $element
     * @return array
     */
    public function getAttributes(ElementInterface $element = null)
    {
        $attribs = empty($this->attributes) ? [] : $this->attributes;
        if ($element) {
            $attribs = array_merge($attribs, $this->getElementAttributes($element));
        }
        return $attribs;
    }

    /**
     * @param ElementInterface $element
     * @return array
     */
    protected function getElementAttributes(ElementInterface $element)
    {
        return empty($this->elementNamespace)
            ? []
            : (array) $element->getAttribute($this->elementNamespace);
    }

    /**
     * @param string $attrib
     * @param string $value
     * @param ElementInterface $element
     * @return self
     */
    public function setLabelAttribute($attrib, $value, ElementInterface $element = null)
    {
    	$attribs = $element ? $this->getElementLabelAttributes($element) : $this->getLabelAttributes();
    	$attribs[$attrib] = $value;
    	$this->setLabelAttributes($attribs, $element);

    	return $this;
    }

    /**
     * @param array|\Traversable $attribs
     * @param ElementInterface $element
     * @return self
     */
    public function setLabelAttributes($attribs, ElementInterface $element = null)
    {
        if ($element && $element instanceof LabelAwareInterface) {
            if (!empty($this->elementNamespace)) {
                $attribs[$this->elementNamespace] = $attribs;
            }
            $element->setLabelAttributes($attribs);
        } elseif (property_exists($this, 'labelAttributes')) {
            $this->labelAttributes = $attribs;
        }

        return $this;
    }

    /**
     * @param ElementInterface $element
     * @return array
     */
    public function getLabelAttributes(ElementInterface $element = null)
    {
        $attribs = [];

        if (property_exists($this, 'labelAttributes')) {
            $attribs = (array) $this->labelAttributes;
        } else {
            return $this->getElementLabelAttributes($element);
        }
        if ($element) {
            $attribs = array_merge($attribs, $this->getElementLabelAttributes($element));
        }

        return $attribs;
    }

    /**
     * @param string $attrib
     * @param ElementInterface $element
     * @return array
     */
    public function getLabelAttribute($attrib, ElementInterface $element = null)
    {
        $attribs = $this->getLabelAttributes($element);
        if (isset($attribs[$attrib])) {
            return $attribs[$attrib];
        }
    }

    /**
     * @param ElementInterface $element
     * @return array
     */
    protected function getElementLabelAttributes(ElementInterface $element)
    {
        if (!$element instanceof LabelAwareInterface) {
            return [];
        }

        if (($attribs = (array) $element->getLabelAttributes())
            && !empty($this->elementNamespace)
            && isset($attribs[$this->elementNamespace]))
        {
            return $attribs[$this->elementNamespace];
        }

        return $attribs;
    }

    /**
     * @param string $option
     * @param mixed $value
     * @param ElementInterface $element
     * @return self
     */
    public function setOption($option, $value, ElementInterface $element = null)
    {
        $options = $element ? $this->getElementOptions($element) : $this->getOptions();
        $options[$option] = $value;
        $this->setOptions($options, $element);

        return $this;
    }

    /**
     * @param array|\Traversable $options
     * @param ElementInterface $element
     * @return self
     */
    public function setOptions($options, ElementInterface $element = null)
    {
    	if ($element) {
    	    empty($this->elementNamespace)
    	       ? $element->setOptions($options)
    	       : $element->setOption($this->elementNamespace, $options);
    	} elseif (property_exists($this, 'options')) {
    		$this->options = $options;
    	}

    	return $this;
    }

    /**
     * @param string $option
     * @param ElementInterface $element
     * @return mixed
     */
    public function getOption($option, ElementInterface $element = null)
    {
        $options = $this->getOptions($element);
        if (isset($options[$option])) {
            return $options[$option];
        }
    }

    /**
     * @param ElementInterface $element
     * @return array
     */
    public function getOptions(ElementInterface $element = null)
    {
        $options = empty($this->options) ? [] : $this->options;

        if ($element) {
        	$options = array_merge($options, $this->getElementOptions($element));
        }

        return $options;
    }

    /**
     * @param ElementInterface $element
     * @return array
     */
    protected function getElementOptions(ElementInterface $element)
    {
        return empty($this->elementNamespace)
            ? []
            : (array) $element->getOption($this->elementNamespace);
    }
}
