<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper\Decorator;

use Zend\Form\Element,
    Zend\Form\ElementInterface,
    Zend\Form\FieldsetInterface,
    Zend\Form\FormInterface,
    Zend\Form\View\Helper\AbstractHelper,
    CmsCommon\Form\View\Helper\FormElement,
    CmsCommon\View\Helper\HtmlContainer;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
abstract class AbstractHtmlContainer extends HtmlContainer
{
    /**
     * @param string|ElementInterface $content
     * @param array $attribs
     * @param ElementInterface $element
     * @param FormInterface $form
     */
    public function __invoke($content = null, array $attribs = [], ElementInterface $element = null, FormInterface $form = null)
    {
        if (func_num_args() === 0) {
            return $this;
        }

        return $this->render($content, $attribs, $element, $form);
    }

    /**
     * @param string|ElementInterface $content
     * @param array $attribs
     * @param ElementInterface $element
     * @param FormInterface $form
     */
    public function render($content, array $attribs = [], ElementInterface $element = null, FormInterface $form = null)
    {
        return parent::render($content, $attribs);
    }

    /**
     * Helper method. Checks whether $element has error.
     *
     * @param ElementInterface $element
     * @param FormInterface $form
     * @return bool
     */
    protected function isElementHasError(ElementInterface $element, FormInterface $form)
    {
        if ($element->getMessages()) {
            return true;
        }

        $elements = $this->getInvalidFieldsetElements($element, $form);
        if ($elements && in_array($element, $elements)) {
            return true;
        }

        return false;
    }

    /**
     * Helper method. Retrieves invalid elements from fieldset the $element belongs to.
     *
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
     * Helper method. Retrieves all elements from fieldset the $element belongs to.
     *
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
     * @param string $elementName
     * @param ElementInterface[] $elements
     * @return ElementInterface|null
     */
    protected function getFieldsetElementByName($elementName, array $elements)
    {
        /* @var $element ElementInterface */
        foreach ($elements as $name => $element) {
            if ($elementName === $name) {
                return $element;
            }
        }
    }
}
