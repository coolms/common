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

use Zend\Form\ElementInterface,
    Zend\Form\FormInterface,
    Zend\Form\LabelAwareInterface,
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\I18n\Translator\TranslatorAwareTrait,
    CmsCommon\View\Helper\HtmlContainer;

class Legend extends HtmlContainer implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    /**
     * @var string
     */
    protected $tagName = 'legend';

    /**
     * {@inheritDoc}
     *
     * @param  ElementInterface $element
     * @param  FormInterface $form
     */
    public function __invoke($content = null, array $attribs = [], ElementInterface $element = null, FormInterface $form = null)
    {
        if (func_num_args() === 0) {
            return $this;
        }

        return $this->render($content, $attribs, $element, $form);
    }

    /**
     * @param  string $content
     * @param  array $attribs
     * @param  ElementInterface $element
     * @param  FormInterface $form
     * @return string
     */
    public function render($content, array $attribs = [], ElementInterface $element = null, FormInterface $form = null)
    {
        if (!$content && $element instanceof LabelAwareInterface) {
            $content = $element->getLabel();
        }

        if ($this->isTranslatorEnabled() && null !== ($translator = $this->getTranslator())) {
            $content = $translator->translate($content, $this->getTranslatorTextDomain());
        }

        if ($element) {
            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $escapeHtmlHelper = $this->getEscapeHtmlHelper();
                $content = $escapeHtmlHelper($content);
            }
        }

        return parent::render($content, $attribs);
    }
}
