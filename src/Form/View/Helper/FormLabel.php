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
    CmsCommon\View\Helper\Decorator\Decorator;
use Zend\I18n\Translator\TranslatorAwareInterface;

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
            $decoratorHelper = $this->getDecoratorHelper();
            if ($decoratorHelper instanceof TranslatorAwareInterface) {
                $decoratorRollbackTextDomain = $decoratorHelper->getTranslatorTextDomain();
                if (!$decoratorRollbackTextDomain || $decoratorRollbackTextDomain === 'default') {
                    $decoratorHelper->setTranslatorTextDomain($this->getTranslatorTextDomain());
                }
            }

            $markup = $decoratorHelper($labelContent, $decorators, $element);

            if (isset($decoratorRollbackTextDomain)) {
                $decoratorHelper->setTranslatorTextDomain($decoratorRollbackTextDomain);
            }

        } else {
            if (!$element->getLabel() && null === $labelContent) {
                $labelContent = '';
                $position = null;
            }

            $markup = parent::__invoke($element, $labelContent, $position);
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
        $decorators = (array) $element->getLabelOption($this->getDecoratorNamespace());
        return $decorators;
    }

    /**
     * @return string
     */
    public function getDecoratorNamespace()
    {
        return $this->decoratorNamespace;
    }
}
