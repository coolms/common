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

use Zend\Form\Element\Captcha,
    Zend\Form\ElementInterface,
    Zend\I18n\Translator\Translator,
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\View\Helper\AbstractHelper;

trait TranslatorTrait
{
    /**
     * @param ElementInterface $element
     * @param AbstractHelper $helper
     * @return string
     */
    protected function renderTranslated(ElementInterface $element, AbstractHelper $helper)
    {
        if (!$helper instanceof TranslatorAwareInterface || !$helper->isTranslatorEnabled()) {
            return $helper($element);
        }

        $translator = $helper->getTranslator();
        if (!($isEventManagerEnabled = $translator->isEventManagerEnabled())) {
            $translator->enableEventManager();
        }

        $translatorEventManager = $translator->getEventManager();
        $rollbackTextDomain     = $helper->getTranslatorTextDomain();

        if ($element instanceof Captcha) {
            $captchaHelper = $this->getView()->plugin($element->getCaptcha()->getHelperName());
            $captchaHelper->setTranslatorTextDomain($rollbackTextDomain);
        }

        $textDomain = $element->getOption('text_domain');
        if (!$rollbackTextDomain || $rollbackTextDomain === 'default') {
            $helper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        if ($textDomain !== $helper->getTranslatorTextDomain()) {
            $callbackHandler = $translatorEventManager->attach(
                Translator::EVENT_MISSING_TRANSLATION,
                function($e) use ($translator, $textDomain) {
                    if ($e->getParam('text_domain') !== $textDomain) {
                        return $translator->translate($e->getParam('message'), $textDomain, $e->getParam('locale'));
                    }

                    return $e->getParam('message');
                }
            );
        }

        $markup = $helper($element);

        if (isset($callbackHandler)) {
            $translatorEventManager->detach($callbackHandler);
        }

        if (!$isEventManagerEnabled) {
            $translator->disableEventManager();
        }

        return $markup;
    }
}
