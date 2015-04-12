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

use Zend\Form\ElementInterface,
    Zend\Form\LabelAwareInterface,
    Zend\Form\View\Helper\FormLabel,
    Zend\I18n\Translator\TranslatorAwareInterface,
    CmsCommon\View\Helper\HtmlContainer;

class ElementLabel extends HtmlContainer
{
    /**
     * @var string
     */
    protected $tagName = 'label';

    /**
     * @var FormLabel
     */
    protected $labelHelper;

    /**
     * {@inheritDoc}
     *
     * @param ElementInterface $element
     */
    public function __invoke($content = null, array $attribs = [], ElementInterface $element = null)
    {
        if (func_num_args() === 0) {
            return $this;
        }

        return $this->render($content, $attribs, $element);
    }

    /**
     * @param  string $content
     * @param  array $attribs
     * @param  ElementInterface $element
     * @return string
     */
    public function render($content, array $attribs = [], ElementInterface $element = null)
    {
        if (!$element instanceof LabelAwareInterface) {
            return parent::render($content, $attribs);
        }

        $element->setLabelAttributes($this->mergeAttributes($attribs));

        $labelHelper = $this->getLabelHelper();

        if ($labelHelper instanceof TranslatorAwareInterface
            && ($textDomain = $element->getOption('text_domain'))
        ) {
            $rollbackTextDomain = $labelHelper->getTranslatorTextDomain();
            $labelHelper->setTranslatorTextDomain($textDomain);
        }

        $markup = $labelHelper($element, $content ?: null, $this->getLabelPosition($element));

        if ($textDomain) {
            $labelHelper->setTranslatorTextDomain($rollbackTextDomain);
        }

        return $markup;
    }

    /**
     * @return FormLabel
     */
    protected function getLabelHelper()
    {
        if ($this->labelHelper) {
            return $this->labelHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->labelHelper = $this->view->plugin('form_label');
        }

        if (!$this->labelHelper instanceof FormLabel) {
            $this->labelHelper = new FormLabel();
            $this->labelHelper->setView($this->getView());
        }

        return $this->labelHelper;
    }

    /**
     * @param LabelAwareInterface $element
     * @return string
     */
    protected function getLabelPosition(LabelAwareInterface $element)
    {
        if ($element->getLabelOption('position')) {
            return $element->getLabelOption('position');
        }

        switch ($element->getAttribute('type')) {
            case 'radio':
            case 'checkbox':
                return FormLabel::APPEND;
            default:
                return FormLabel::PREPEND;
        }
    }
}
