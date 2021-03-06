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
    Zend\Form\FieldsetInterface,
    Zend\Form\FormInterface,
    Zend\Form\View\Helper\FormElement,
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\I18n\Translator\TranslatorAwareTrait,
    CmsCommon\Form\View\Helper\FormRow,
    CmsCommon\View\Helper\IdNormalizer;

class Element extends AbstractHtmlContainer implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    /**
     * @var string
     */
    protected $tagName = 'input';

    /**
     * @var string
     */
    protected $defaultElementHelper = 'formElement';

    /**
     * @var FormElement
     */
    protected $elementHelper;

    /**
     * @var string
     */
    protected $defaultIdNormalizer = 'idNormalizer';

    /**
     * @var IdNormalizer
     */
    protected $idNormalizer;

    /**
     * {@inheritDoc}
     */
    public function render(
        $content,
        array $attribs = [],
        ElementInterface $element = null,
        FormInterface $form = null
    ) {
        if (is_string($content) && $element && $form) {
            $elements = $this->getFieldsetElements($element, $form);
            if (isset($elements[$content])) {
                $content = $elements[$content];
            }
        }

        if ($content instanceof ElementInterface) {
            $content->setAttributes($this->mergeAttributes($attribs));
            if (!$content->hasAttribute('id')) {
                $idNormalizer = $this->getIdNormalizer();
                $content->setAttribute('id', $idNormalizer($content->getName()));
            }

            if ($content instanceof FieldsetInterface) {
                $rendered = $this->renderHelper($content, false, true);
            } else {
                $rendered = $this->renderHelper($content, $form);
            }

            $content->setOption(FormRow::RENDERED, true);

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
            $this->elementHelper = $this->view->plugin($this->defaultElementHelper);
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
            $this->idNormalizer = $this->view->plugin($this->defaultIdNormalizer);
        }

        if (!$this->idNormalizer instanceof IdNormalizer) {
            $this->idNormalizer = new IdNormalizer();
        }

        return $this->idNormalizer;
    }

    /**
     * @param ElementInterface $element
     * @return string
     */
    protected function renderHelper(ElementInterface $element)
    {
        $helper = $this->getElementHelper();
        if ($helper instanceof TranslatorAwareInterface) {
            $helperRollbackTextDomain = $helper->getTranslatorTextDomain();
            $helper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $markup = call_user_func_array($helper, func_get_args());

        if (isset($helperRollbackTextDomain)) {
            $helper->setTranslatorTextDomain($helperRollbackTextDomain);
        }

        return $markup;
    }
}
