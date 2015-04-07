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

use Zend\Form\ElementInterface,
    Zend\Form\LabelAwareInterface;

/**
 * Base functionality for all form view helpers
 */
trait LabelAttributesTrait
{

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
}
