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
    Zend\I18n\Translator\Translator,
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\View\Helper\AbstractHelper;

trait TranslatorTrait
{
    /**
     * @param AbstractHelper $helper
     * @param ElementInterface $element
     * @return string
     */
    protected function renderTranslated(AbstractHelper $helper, ElementInterface $element)
    {
        if (!$helper instanceof TranslatorAwareInterface || !$helper->isTranslatorEnabled()) {
            if (func_num_args() > 2) {
                $param_arr = array_slice(func_get_args(), 1);
                return call_user_func_array($helper, $param_arr);
            }

            return $helper($element);
        }

        $translator = $helper->getTranslator();
        if (!($isEventManagerEnabled = $translator->isEventManagerEnabled())) {
            $translator->enableEventManager();
        }

        $translatorEventManager = $translator->getEventManager();

        $rollbackTextDomain = $helper->getTranslatorTextDomain();
        if (!$rollbackTextDomain || $rollbackTextDomain === 'default') {
            $helper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $textDomain = $element->getOption('text_domain');
        if ($textDomain && $textDomain !== $helper->getTranslatorTextDomain()) {
            $callbackHandler = $translatorEventManager->attach(
                Translator::EVENT_MISSING_TRANSLATION,
                function($e) use ($translator, $textDomain) {
                    if ($e->getParam('text_domain') !== $textDomain) {
                        return $translator->translate(
                                $e->getParam('message'),
                                $textDomain,
                                $e->getParam('locale')
                            );
                    }

                    return $e->getParam('message');
                }
            );
        }

        if (func_num_args() > 2) {
            $param_arr = array_slice(func_get_args(), 1);
            $markup = call_user_func_array($helper, $param_arr);
        } else {
            $markup = $helper($element);
        }

        if (isset($callbackHandler)) {
            $translatorEventManager->detach($callbackHandler);
        }

        if (!$isEventManagerEnabled) {
            $translator->disableEventManager();
        }

        return $markup;
    }
}
