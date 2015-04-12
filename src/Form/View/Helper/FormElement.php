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
    Zend\Form\View\Helper\FormElement as ZendFormElement,
    Zend\Form\View\Helper\FormInput,
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\I18n\Translator\TranslatorAwareTrait,
    CmsCommon\View\Helper\Decorator\DecoratorProviderInterface;

class FormElement extends ZendFormElement implements
        DecoratorProviderInterface,
        TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    /**
     * __construct
     */
    public function __construct()
    {
        if (!isset($this->classMap['Zend\Form\Fieldset'])) {
            $this->addClass('Zend\Form\Fieldset', 'formcollection');
        }

        if (!isset($this->typeMap['static'])) {
            $this->addType('static', 'formstatic');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDecoratorSpecification(ElementInterface $element = null)
    {
        if ($element) {
            $elementHelper = $this->getElementHelper($element);
            if ($elementHelper instanceof DecoratorProviderInterface) {
                return $elementHelper->getDecoratorSpecification();
            }
        }

        return [];
    }

    /**
     * @return FormInput
     */
    protected function getElementHelper(ElementInterface $element)
    {
        $renderer = $this->getView();

        foreach ($this->classMap as $class => $pluginName) {
            if ($element instanceof $class) {
                return $renderer->plugin($pluginName);
            }
        }

        $type = $element->getAttribute('type');
        if (isset($this->typeMap[$type])) {
            return $renderer->plugin($this->typeMap[$type]);
        }

        return $renderer->plugin($this->defaultHelper);
    }

    /**
     * {@inheritDoc}
     */
    protected function renderHelper($name, ElementInterface $element)
    {
        $helper = $this->getView()->plugin($name);

        if ($helper instanceof TranslatorAwareInterface) {
            $rollbackTextDomain = $helper->getTranslatorTextDomain();
            $helper->setTranslatorTextDomain($element->getOption('text_domain') ?: $this->getTranslatorTextDomain());
        }

        $markup = $helper($element);

        if ($helper instanceof TranslatorAwareInterface) {
            $helper->setTranslatorTextDomain($rollbackTextDomain);
        }

        return $markup;
    }
}
