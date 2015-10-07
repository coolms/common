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
    Zend\Form\FormInterface,
    Zend\Form\LabelAwareInterface,
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\I18n\Translator\TranslatorAwareTrait,
    CmsCommon\View\Helper\HtmlContainer,
    CmsCommon\View\Helper\TranslatorTrait;

class Label extends HtmlContainer implements TranslatorAwareInterface
{
    use TranslatorAwareTrait,
        TranslatorTrait;

    /**
     * @var string
     */
    protected $tagName = 'label';

    /**
     * {@inheritDoc}
     *
     * @param  ElementInterface $element
     */
    public function __invoke(
        $content = null,
        array $attribs = [],
        ElementInterface $element = null
    ) {
        if (func_num_args() === 0) {
            return $this;
        }

        return $this->render($content, $attribs, $element);
    }

    /**
     * {@inheritDoc}
     *
     * @param  ElementInterface $element
     */
    public function render(
        $content,
        array $attribs = [],
        ElementInterface $element = null
    ) {
        $fallbackTextDomain = 'default';

        if ($content && $content instanceof LabelAwareInterface) {
            $fallbackTextDomain = $content->getOption('text_domain');
            $content = $content->getLabel();
        } elseif (!$content && $element instanceof LabelAwareInterface) {
            $fallbackTextDomain = $element->getOption('text_domain');
            $content = $element->getLabel();
        }

        $content = $this->translate($content, $fallbackTextDomain);

        if ($element instanceof FormInterface
            && ($object = $element->getObject())
            && method_exists($object, '__toString')
        ) {
            $content = sprintf($content, $object);
        }

        if ($element
            && (!$element instanceof LabelAwareInterface
                || !$element->getLabelOption('disable_html_escape'))
        ) {
            $escapeHtmlHelper = $this->getEscapeHtmlHelper();
            $content = $escapeHtmlHelper($content);
        }

        return parent::render($content, $attribs);
    }
}
