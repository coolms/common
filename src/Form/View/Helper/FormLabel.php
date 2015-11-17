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
    Zend\Form\LabelAwareInterface,
    Zend\Form\View\Helper\FormLabel as ZendFormLabel,
    Zend\I18n\Translator\TranslatorAwareInterface,
    CmsCommon\View\Helper\Decorator\Decorator;

class FormLabel extends ZendFormLabel
{
    /**
     * @var string
     */
    protected $defaultDecoratorHelper = 'decorator';

    /**
     * @var Decorator
     */
    protected $decoratorHelper;

    /**
     * @var string
     */
    protected $decoratorNamespace = Decorator::OPTION_KEY;

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        ElementInterface $element = null,
        $labelContent = null,
        $position = null
    ) {
        if (!$element) {
            return $this;
        }

        if ($decorators = $this->getDecorators($element)) {
            $helper = $this->getDecoratorHelper();
            $args   = [$labelContent, $decorators, $element];
        } else {
            if (!$element->getLabel() && null === $labelContent) {
                $labelContent = '';
                $position = null;
            }

            $helper = [__CLASS__, 'parent::' . __FUNCTION__];
            $args   = [$element, $labelContent, $position];
        }

        if ($helper instanceof TranslatorAwareInterface) {
            $rollbackTextDomain = $helper->getTranslatorTextDomain();
            $helper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        $markup = call_user_func_array($helper, $args);

        if (isset($rollbackTextDomain)) {
            $helper->setTranslatorTextDomain($rollbackTextDomain);
        }

        return $markup;
    }

    /**
     * @return Decorator
     */
    protected function getDecoratorHelper()
    {
        if ($this->decoratorHelper) {
            return $this->decoratorHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->decoratorHelper = $this->view->plugin($this->defaultDecoratorHelper);
        }

        if (!$this->decoratorHelper instanceof Decorator) {
            $this->decoratorHelper = new Decorator();
            $this->decoratorHelper->setView($this->getView());
        }

        return $this->decoratorHelper;
    }

    /**
     * @param LabelAwareInterface $element
     * @return array
     */
    protected function getDecorators(LabelAwareInterface $element)
    {
        return (array) $element->getLabelOption($this->getDecoratorNamespace());
    }

    /**
     * @return string
     */
    public function getDecoratorNamespace()
    {
        return $this->decoratorNamespace;
    }
}
