<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper;

use Zend\I18n\Translator\Translator,
    Zend\I18n\Translator\TranslatorInterface;

trait TranslatorTrait
{
    /**
     * @param string $message
     * @param string $fallbackTextDomain
     * @param string $locale
     * @return string
     */
    protected function translate($message, $fallbackTextDomain = 'default', $locale = null)
    {
        if (!($this->isTranslatorEnabled() && null !== ($translator = $this->getTranslator()))) {
            return $message;
        }

        if (!($isEventManagerEnabled = $translator->isEventManagerEnabled())) {
            $translator->enableEventManager();
        }

        $translatorEventManager = $translator->getEventManager();
        $translatorTextDomain = $this->getTranslatorTextDomain();

        if (null === $fallbackTextDomain) {
            $fallbackTextDomain = 'default';
        }

        if ($fallbackTextDomain && $fallbackTextDomain !== $translatorTextDomain) {
            $callbackHandler = $translatorEventManager->attach(
                Translator::EVENT_MISSING_TRANSLATION,
                function($e) use ($translator, $fallbackTextDomain) {
                    if ($e->getParam('text_domain') !== $fallbackTextDomain) {
                        return $translator->translate(
                                $e->getParam('message'),
                                $fallbackTextDomain,
                                $e->getParam('locale')
                            );
                    }

                    return $e->getParam('message');
                }
            );
        }

        $message = $translator->translate($message, $translatorTextDomain, $locale);

        if (isset($callbackHandler)) {
            $translatorEventManager->detach($callbackHandler);
        }

        if (!$isEventManagerEnabled) {
            $translator->disableEventManager();
        }

        return $message;
    }

    /**
     * Returns translator used in object
     *
     * @return TranslatorInterface
     */
    abstract public function getTranslator();

    /**
     * Returns whether translator is enabled and should be used
     *
     * @return bool
     */
    abstract public function isTranslatorEnabled();

    /**
     * Return the translation text domain
     *
     * @return string
     */
    abstract public function getTranslatorTextDomain();
}
