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
    Zend\I18n\Translator;

class FormRow extends ZendFormRow
{
    use FormProviderTrait,
        DecoratorTrait;

    const DEFAULT_TEXT_DOMAIN = 'default';

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

        if ($element->getOption('__rendered__') ||
            ($element->getAttribute('type') === 'static' &&
                ($this->getRenderMode() === static::RENDER_DYNAMIC ||
                    null === $element->getValue()))
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
        static $priority = 10;

        if ($decorators = $this->getDecorators($element, $this->getForm())) {
            $helper = $this->getDecoratorHelper();
            $args   = [$element, $decorators, $element, $this->getForm()];
        } elseif ($element instanceof FieldsetInterface) {
            $helper    = $this->getElementHelper();
            $args      = [$element];
            if (!$element->getOption('translation_priority')) {
                $priority += 10;
                $element->setOption('translation_priority', $priority);
            }
        } else {
            $helper = [__CLASS__, 'parent::' . __FUNCTION__];
            $args   = func_get_args();
        }

        $rollbackTextDomain = $this->getTranslatorTextDomain();
        $textDomain = $element->getOption('text_domain');

        if ($textDomain && $rollbackTextDomain === static::DEFAULT_TEXT_DOMAIN) {
            $this->setTranslatorTextDomain($textDomain);
        }

        if (null !== ($translator = $this->getTranslator())) {
            if (!($isEventManagerEnabled = $translator->isEventManagerEnabled())) {
                $translator->enableEventManager();
            }

            $callbackHandler = $translator->getEventManager()->attach(
                Translator\Translator::EVENT_MISSING_TRANSLATION,
                function($e) use ($translator, $textDomain, $rollbackTextDomain) {
                    $textDomain = $textDomain ?: $rollbackTextDomain;
                    if ($textDomain !== $rollbackTextDomain) {
                        $message = $e->getParam('message');
                        if ($e->getParam('text_domain') !== $textDomain) {
                            $translated = $translator->translate(
                                $message,
                                $textDomain,
                                $e->getParam('locale')
                            );

                            return $translated === $message ? null : $translated;
                        }

                        return $message;
                    }
                },
                $element->getOption('translation_priority')
            );
        }

        if ($helper instanceof Translator\TranslatorAwareInterface) {
            $helperRollbackTextDomain = $helper->getTranslatorTextDomain();
            $helper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $elementHelper = $this->getElementHelper();
        if ($elementHelper instanceof Translator\TranslatorAwareInterface) {
            $elementRollbackTextDomain = $elementHelper->getTranslatorTextDomain();
            $elementHelper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $labelHelper = $this->getLabelHelper();
        if ($labelHelper instanceof Translator\TranslatorAwareInterface) {
            $labelRollbackTextDomain = $labelHelper->getTranslatorTextDomain();
            $labelHelper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $markup = call_user_func_array($helper, $args);

        if (null !== $translator && isset($callbackHandler)) {
            $translator->getEventManager()->detach($callbackHandler);
            if (!$isEventManagerEnabled) {
                $translator->disableEventManager();
            }
        }

        if (isset($helperRollbackTextDomain)) {
            $helper->setTranslatorTextDomain($helperRollbackTextDomain);
        }

        if (isset($elementRollbackTextDomain)) {
            $elementHelper->setTranslatorTextDomain($elementRollbackTextDomain);
        }

        if (isset($labelRollbackTextDomain)) {
            $elementHelper->setTranslatorTextDomain($labelRollbackTextDomain);
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
        if ($this->getRenderMode() === static::RENDER_STATIC &&
            method_exists($renderer, 'plugin')
        ) {
            return $renderer->plugin('form_static');
        }

        return parent::getElementHelper();
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
