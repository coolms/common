<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
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
     * @return self|string
     */
    public function __invoke(
        $content = null,
        array $attribs = [],
        ElementInterface $element = null,
        FormInterface $form = null
    ) {
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
     * @return string
     */
    public function render(
        $content,
        array $attribs = [],
        ElementInterface $element = null,
        FormInterface $form = null
    ) {
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

        $elementName = $this->normalizeElementName($element->getName());
        $elements = $this->getInvalidFieldsetElements($element, $form);
        if (isset($elements[$elementName]) && $elements[$elementName] === $element) {
            return true;
        }

        return false;
    }

    /**
     * Helper method. Retrieves invalid elements from fieldset the $element belongs to.
     *
     * @param ElementInterface $element
     * @param FieldsetInterface $fieldset
     * @return array|\Traversable
     */
    protected function getInvalidFieldsetElements(ElementInterface $element, FieldsetInterface $fieldset)
    {
        $elementName = $this->normalizeElementName($element->getName());
        $elements = $fieldset->getElements();

        if (isset($elements[$elementName]) && $elements[$elementName] === $element) {
            if ($fieldset->getMessages()) {
                return $elements;
            }

            /* @var $fieldsetElement ElementInterface */
            foreach ($elements as $fieldsetElement) {
                if ($fieldsetElement->getMessages()
                    && ($fieldsetElement->getAttribute('type') === 'hidden'
                        || $fieldsetElement instanceof Element\Captcha)
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

        return [];
    }

    /**
     * Helper method. Retrieves all elements from fieldset the $element belongs to.
     *
     * @param ElementInterface $element
     * @param FieldsetInterface $fieldset
     * @return array|\Traversable
     */
    protected function getFieldsetElements(ElementInterface $element, FieldsetInterface $fieldset)
    {
        $elementName = $this->normalizeElementName($element->getName());

        if ($fieldset->has($elementName) && $fieldset->get($elementName) === $element) {
            return $fieldset->getElements();
        }

        if ($fieldset instanceof Element\Collection && (
            ($elements = $this->getFieldsetElements($element, $fieldset->getTargetElement())) ||
            ($elements = $this->getFieldsetElements($element, $fieldset->getTemplateElement()))
        )) {
            return $elements;
        }

        foreach ($fieldset->getFieldsets() as $fieldsetElement) {
            if ($elements = $this->getFieldsetElements($element, $fieldsetElement)) {
                return $elements;
            }
        }

        return [];
    }

    /**
     * @param string $name
     * @return string
     */
    private function normalizeElementName($name)
    {
        if (strpos($name, '[') === false) {
            return $name;
        }

        return trim(strrchr($name, '['), '[]');
    }
}
