<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper\Traits;

use Zend\Form\ElementInterface;

/**
 * Base functionality for all form view helpers
 */
trait OptionsTrait
{
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
