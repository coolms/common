<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper;

use Zend\Captcha\AdapterInterface as CaptchaAdapter,
    Zend\Form\ElementInterface,
    Zend\Form\Exception,
    Zend\Form\View\Helper\FormCaptcha as ZendFormCaptcha,
    Zend\Form\View\Helper\FormInput,
    Zend\I18n\Translator\TranslatorAwareInterface;

class FormCaptcha extends ZendFormCaptcha
{
    /**
     * {@inheritDoc}
     */
    public function render(ElementInterface $element)
    {
        $captcha = $element->getCaptcha();

        if ($captcha === null || !$captcha instanceof CaptchaAdapter) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute implementing Zend\Captcha\AdapterInterface; none found',
                __METHOD__
            ));
        }

        $helper = $captcha->getHelperName();

        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the renderer implements plugin(); it does not',
                __METHOD__
            ));
        }

        $helper = $renderer->plugin($helper);
        if ($helper instanceof TranslatorAwareInterface) {
            $rollbackTextDomain = $helper->getTranslatorTextDomain();
            $helper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $markup = parent::render($element);

        if ($helper instanceof TranslatorAwareInterface) {
            $helper->setTranslatorTextDomain($rollbackTextDomain);
        }

        return $markup;
    }
}
