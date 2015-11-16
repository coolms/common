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
    Zend\I18n\Translator\Translator,
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
     * @var string
     */
    private $defaultTextDomain = 'default';

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
        if ($decorators = $this->getDecorators($element, $this->getForm())) {
            $helper = $this->getDecoratorHelper();
            $args   = [$element, $decorators, $element, $this->getForm()];
        } elseif ($element instanceof FieldsetInterface) {
            $helper = $this->getElementHelper();
            $args   = [$element];
        } else {
            $helper = [__CLASS__, 'parent::' . __FUNCTION__];
            $args   = func_get_args();
        }

        $rollbackTextDomain = $this->getTranslatorTextDomain();
        $textDomain = $element->getOption('text_domain');
        if ($rollbackTextDomain === $this->defaultTextDomain && $textDomain) {
            $this->setTranslatorTextDomain($textDomain);
        }

            $translator = $this->getTranslator();
            if (!($isEventManagerEnabled = $translator->isEventManagerEnabled())) {
                $translator->enableEventManager();
            }

            static $count = 0;
            $translatorEventManager = $translator->getEventManager();
            $callbackHandler = $translatorEventManager->attach(
                Translator::EVENT_MISSING_TRANSLATION,
                function($e) use ($translator, $textDomain, $rollbackTextDomain, &$count) {

                    $textDomain = $textDomain ?: $rollbackTextDomain;
                    //echo "{$e->getParam('text_domain')} :: $textDomain :: $rollbackTextDomain {$e->getParam('message')} <br>";
                    if ($e->getParam('text_domain') !== $textDomain) {
                        if ($count > 100) {
                            //exit;
                        }

                        $count++;
                        return $translator->translate(
                            $e->getParam('message'),
                            $textDomain,
                            $e->getParam('locale')
                        );
                    }
                }
            );

        if ($helper instanceof TranslatorAwareInterface) {
            $helperRollbackTextDomain = $helper->getTranslatorTextDomain();
            $helper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $elementHelper = $this->getElementHelper();
        if ($elementHelper instanceof TranslatorAwareInterface) {
            $elementRollbackTextDomain = $elementHelper->getTranslatorTextDomain();
            $elementHelper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $labelHelper = $this->getLabelHelper();
        if ($labelHelper instanceof TranslatorAwareInterface) {
            $labelRollbackTextDomain = $labelHelper->getTranslatorTextDomain();
            $labelHelper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $markup = call_user_func_array($helper, $args);

        if (isset($callbackHandler)) {
            $translatorEventManager->detach($callbackHandler);
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
        if ($this->getRenderMode() === static::RENDER_STATIC && method_exists($renderer, 'plugin')) {
            return $renderer->plugin('form_static');
        } else {
            return parent::getElementHelper();
        }
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
