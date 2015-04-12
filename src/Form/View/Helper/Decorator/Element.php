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

use Zend\Form\Element as ZendElement,
    Zend\Form\ElementInterface,
    Zend\Form\FormInterface,
    Zend\Form\View\Helper\FormElement,
    CmsCommon\View\Helper\IdNormalizer;

class Element extends AbstractHtmlContainer
{
    /**
     * @var string
     */
    protected $tagName = 'input';

    /**
     * @var AbstractHelper
     */
    protected $elementHelper;

    /**
     * @var IdNormalizer
     */
    protected $idNormalizer;

    /**
     * @param  string|ElementInterface $content
     * @param  array $attribs
     * @param  ElementInterface $element
     * @param  FormInterface $form
     * @return string
     */
    public function render($content, array $attribs = [], ElementInterface $element = null, FormInterface $form = null)
    {
        if (is_string($content) && $element && $form) {
            $elements = $this->getFieldsetElements($element, $form);
            $content  = $this->getFieldsetElementByName($content, $elements);
        }

        if ($content instanceof ElementInterface) {
            $content->setAttributes($this->mergeAttributes($attribs));
            if (!$content->hasAttribute('id')) {
                $idNormalizer = $this->getIdNormalizer();
                $content->setAttribute('id', $idNormalizer($content->getName()));
            }

            $rendered = $this->renderHelper($content, $form);
            $content->setOption('__rendered__', true);
            return $rendered;
        }

        return parent::render($content, $attribs, $element, $form);
    }

    /**
     * @return FormElement
     */
    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('form_element');
        }

        if (!$this->elementHelper instanceof FormElement) {
            $this->elementHelper = new FormElement();
            $this->elementHelper->setView($this->getView());
        }

        return $this->elementHelper;
    }

    /**
     * @return IdNormalizer
     */
    protected function getIdNormalizer()
    {
        if ($this->idNormalizer) {
            return $this->idNormalizer;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->idNormalizer = $this->view->plugin('id_normalizer');
        }

        if (!$this->idNormalizer instanceof IdNormalizer) {
            $this->idNormalizer = new IdNormalizer();
        }

        return $this->idNormalizer;
    }

    /**
     * @param ElementInterface $element
     * @param FormInterface $form
     * @return string
     */
    protected function renderHelper(ElementInterface $element, FormInterface $form = null)
    {
        $elementHelper = $this->getElementHelper();
        return $elementHelper($element, $form);
    }
}
