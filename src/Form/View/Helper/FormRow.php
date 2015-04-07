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

use Zend\Form\ElementInterface,
    Zend\Form\LabelAwareInterface,
    Zend\Form\View\Helper\FormRow as ZendFormRow,
    Zend\I18n\Translator\TranslatorAwareInterface,
    CmsCommon\View\Helper\Decorator,
    CmsCommon\Form\View\Helper\Traits\FormProviderTrait;
use Zend\Form\FormInterface;
use CmsCommon\View\Helper\DecoratorProviderInterface;

class FormRow extends ZendFormRow
{
    use FormProviderTrait;

    const RENDER_ALL     = 'all';
    const RENDER_STATIC  = 'static';
    const RENDER_DYNAMIC = 'dynamic';

    /**
     * @var string
     */
    protected $renderMode = self::RENDER_ALL;

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
     * 
     * @param string $renderMode
     */
    public function __invoke(ElementInterface $element = null, $labelPosition = null,
            $renderErrors = null, $partial = null, $renderMode = null)
    {
        if (!$element) {
            return $this;
        }

        if (null !== $renderMode) {
            $this->setRenderMode($renderMode);
        }

        return parent::__invoke($element, $labelPosition, $renderErrors, $partial);
    }

    /**
     * {@inheritDoc}
     */
    public function render(ElementInterface $element, FormInterface $form = null)
    {
        if ($element->getOption('__rendered__')
            || ($element->getAttribute('type') === 'static'
                && ($this->getRenderMode() === static::RENDER_DYNAMIC
                    || null === $element->getValue()))
        ) {
            return '';
        }

        if ($decorators = $this->getDecorators($element)) {
            $decoratorHelper = $this->getDecoratorHelper();
            return $decoratorHelper($element, $decorators, $element, $form ?: $this->getForm());
        }

        return parent::render($element);
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementHelper()
    {
        $renderer = $this->getView();
        if ($this->getRenderMode() === static::RENDER_STATIC
            && method_exists($renderer, 'plugin')
        ) {
            $elementHelper = $renderer->plugin('form_static');
        } else {
            $elementHelper = parent::getElementHelper();
        }

        if ($elementHelper instanceof TranslatorAwareInterface) {
            $elementHelper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        return $elementHelper;
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
            $this->decoratorHelper = $this->view->plugin('decorator');
        }

        if (!$this->decoratorHelper instanceof Decorator) {
            $this->decoratorHelper = new Decorator();
        }

        return $this->decoratorHelper;
    }

    /**
     * @param ElementInterface $element
     * @return array
     */
    protected function getDecorators(ElementInterface $element)
    {
        $decorators = (array) $element->getOption($this->decoratorNamespace);

        $helper = $this->getElementHelper();
        if ($helper instanceof DecoratorProviderInterface) {
            return array_replace_recursive($helper->getDecoratorSpecification($element), $decorators);
        }

        return $decorators;
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
