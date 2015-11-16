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
        if (!$helper instanceof TranslatorAwareInterface ||
            !$helper->isTranslatorEnabled() ||
            !$this instanceof TranslatorAwareInterface ||
            !$this->isTranslatorEnabled()
        ) {
            if (func_num_args() > 2) {
                $param_arr = array_slice(func_get_args(), 1);
                return call_user_func_array($helper, $param_arr);
            }

            return $helper($element);
        }

        $textDomain = $this->getTranslatorTextDomain();
        $rollbackTextDomain = $helper->getTranslatorTextDomain();

        if ($rollbackTextDomain && $rollbackTextDomain !== $textDomain) {

            $translator = $this->getTranslator();
            if (!($isEventManagerEnabled = $translator->isEventManagerEnabled())) {
                $translator->enableEventManager();
            }

            $translatorEventManager = $translator->getEventManager();
            $callbackHandler = $translatorEventManager->attach(
                Translator::EVENT_MISSING_TRANSLATION,
                function($e) use ($translator, $textDomain, $rollbackTextDomain, $element) {
                    echo $element->getName() . ' ' . $e->getParam('text_domain') . ' ' . $textDomain . ' ' . $e->getParam('message') . "<br>";
                    
                    if ($e->getParam('text_domain') !== $rollbackTextDomain) {
                        return $translator->translate(
                                $e->getParam('message'),
                                $rollbackTextDomain,
                                $e->getParam('locale')
                            );
                    }

                    return $e->getParam('message');
                }
            );
        }

        $helper->setTranslatorTextDomain($textDomain);
        if (func_num_args() > 2) {
            $param_arr = array_slice(func_get_args(), 1);
            $markup = call_user_func_array($helper, $param_arr);
        } else {
            $markup = $helper($element);
        }
        $helper->setTranslatorTextDomain($rollbackTextDomain);

        if (isset($callbackHandler)) {
            $translatorEventManager->detach($callbackHandler);
        
            if (!$isEventManagerEnabled) {
                $translator->disableEventManager();
            }
        }

        return $markup;
    }
}
