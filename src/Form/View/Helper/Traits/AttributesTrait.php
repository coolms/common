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
trait AttributesTrait
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
}
