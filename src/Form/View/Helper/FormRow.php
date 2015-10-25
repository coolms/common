<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper;

use Zend\Form\ElementInterface,
    Zend\Form\FieldsetInterface,
    Zend\Form\FormInterface,
    Zend\Form\LabelAwareInterface,
    Zend\Form\View\Helper\FormRow as ZendFormRow,
    Zend\I18n\Translator\TranslatorAwareInterface;

class FormRow extends ZendFormRow
{
    use FormProviderTrait,
        DecoratorTrait;

    const RENDER_ALL     = 'all';
    const RENDER_STATIC  = 'static';
    const RENDER_DYNAMIC = 'dynamic';

    /**
     * @var string
     */
    protected $renderMode = self::RENDER_ALL;

    /**
     * {@inheritDoc}
     *
     * @param string $renderMode
     */
    public function __invoke(
        ElementInterface $element = null,
        $labelPosition = null,
        $renderErrors = null,
        $partial = null,
        $renderMode = null
    ) {
        if (!$element) {
            return $this;
        }

        if (null !== $renderMode) {
            $this->setRenderMode($renderMode);
        }

        if ($element->getOption('__rendered__')
            || ($element->getAttribute('type') === 'static'
                && ($this->getRenderMode() === static::RENDER_DYNAMIC
                    || null === $element->getValue()))
        ) {
            return '';
        }

        return parent::__invoke($element, $labelPosition, $renderErrors, $partial);
    }

    /**
     * {@inheritDoc}
     */
    public function render(ElementInterface $element, $labelPosition = null)
    {
        $rollbackTextDomain = $this->getTranslatorTextDomain();
        if ((!$rollbackTextDomain || $rollbackTextDomain === 'default') &&
            ($textDomain = $element->getOption('text_domain'))
        ) {
            $this->setTranslatorTextDomain($textDomain);
        }

        if ($decorators = $this->getDecorators($element, $this->getForm())) {
            $decoratorHelper = $this->getDecoratorHelper();
            if ($decoratorHelper instanceof TranslatorAwareInterface) {
                $decoratorRollbackTextDomain = $decoratorHelper->getTranslatorTextDomain();
                if (!$decoratorRollbackTextDomain || $decoratorRollbackTextDomain === 'default') {
                    $decoratorHelper->setTranslatorTextDomain($this->getTranslatorTextDomain());
                }
            }

            $markup = $decoratorHelper($element, $decorators, $element, $this->getForm());

            if (isset($decoratorRollbackTextDomain)) {
                $decoratorHelper->setTranslatorTextDomain($decoratorRollbackTextDomain);
            }

        } elseif ($element instanceof FieldsetInterface) {
            $helper = $this->getElementHelper();
            $markup = $helper($element);
        } else {
            $markup = parent::render($element, $labelPosition);
        }

        $this->setTranslatorTextDomain($rollbackTextDomain);

        return $markup;
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementHelper()
    {
        $renderer = $this->getView();
        if ($this->getRenderMode() === static::RENDER_STATIC && method_exists($renderer, 'plugin')) {
            $elementHelper = $renderer->plugin('form_static');
        } else {
            $elementHelper = parent::getElementHelper();
        }

        if ($elementHelper instanceof TranslatorAwareInterface) {
            $elementHelper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        return $elementHelper;
    }

    /**
     * @param string $mode
     * @return self
     */
    public function setRenderMode($mode)
    {
        $this->renderMode = $mode;
        return $this;
    }

    /**
     * @return string
     */
    public function getRenderMode()
    {
        return $this->renderMode;
    }
}
