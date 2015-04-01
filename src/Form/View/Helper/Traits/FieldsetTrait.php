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
    Zend\Form\Element\Collection,
    Zend\Form\FieldsetInterface;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait FieldsetTrait
{
    /**
     * @param ElementInterface $element
     * @param FieldsetInterface $fieldset
     * @return array|\Traversable|null
     */
    protected function getFieldsetElements(ElementInterface $element, FieldsetInterface $fieldset)
    {
        if (($elements = $fieldset->getElements()) && in_array($element, $elements, true)) {
            return $elements;
        }

        if ($fieldset instanceof Collection
            && ($templateElement = $fieldset->getTemplateElement())
            && ($elements = $templateElement->getElements())
            && in_array($element, $elements, true)
        ) {
            return $elements;
        }

        foreach ($fieldset->getFieldsets() as $fieldsetElement) {
            if ($elements = $this->getFieldsetElements($element, $fieldsetElement)) {
                return $elements;
            }
        }
    }

    /**
     * @param ElementInterface $element
     * @param FieldsetInterface $fieldset
     * @return array|\Traversable|null
     */
    protected function getInvalidFieldsetElements(ElementInterface $element, FieldsetInterface $fieldset)
    {
        $elements = $fieldset->getElements();

        if (in_array($element, $elements)) {
            /* @var $fieldsetElement ElementInterface */
            foreach ($elements as $fieldsetElement) {
                if ($fieldsetElement->getAttribute('type') === 'hidden'
                    && $fieldsetElement->getMessages()
                ) {
                    return $elements;
                }
            }
        }

        foreach ($fieldset->getFieldsets() as $fieldsetElement) {
            if ($elements = $this->getInvalidFieldsetElements($element, $fieldsetElement)) {
                return $elements;
            }
        }
    }

    /**
     * @param string $elementName
     * @param ElementInterface[] $elements
     * @return ElementInterface|null
     */
    protected function getFieldsetElement($elementName, array $elements)
    {
        /* @var $element ElementInterface */
        foreach ($elements as $name => $element) {
            if ($elementName === $name) {
                return $element;
            }
        }
    }
}
