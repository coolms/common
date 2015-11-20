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

use Zend\Form\Element\Csrf,
    Zend\Form\ElementInterface,
    Zend\Form\FieldsetInterface,
    Zend\Form\FormInterface,
    Zend\Form\View\Helper\FormElement as ZendFormElement,
    Zend\I18n\Translator,
    CmsCommon\View\Helper\Decorator\DecoratorProviderInterface;

class FormElement extends ZendFormElement implements
        DecoratorProviderInterface,
        Translator\TranslatorAwareInterface
{
    use Translator\TranslatorAwareTrait;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->addClass(Csrf::class, 'formCsrf');
        $this->addClass(FieldsetInterface::class, 'formCollection');
        $this->addType('static', 'formStatic');
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return call_user_func_array([$this, 'render'], func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        $renderedInstance = call_user_func_array([$this, 'renderInstance'], func_get_args());
        if ($renderedInstance !== null) {
            return $renderedInstance;
        }

        $renderedType = call_user_func_array([$this, 'renderType'], func_get_args());
        if ($renderedType !== null) {
            return $renderedType;
        }

        return $this->renderHelper($this->defaultHelper, $element);
    }

    /**
     * {@inheritDoc}
     */
    public function getDecoratorSpecification(ElementInterface $element = null, FormInterface $form = null)
    {
        if ($element) {
            $elementHelper = $this->getElementHelper($element);
            if ($elementHelper instanceof DecoratorProviderInterface) {
                return $elementHelper->getDecoratorSpecification($element, $form);
            }
        }

        return [];
    }

    /**
     * @return ZendFormElement
     */
    protected function getElementHelper(ElementInterface $element)
    {
        $renderer = $this->getView();

        if ($element->getOption('render_mode') === FormRow::RENDER_STATIC) {
            return $renderer->plugin('formStatic');
        }

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
        if ($element->getOption('render_mode') === FormRow::RENDER_STATIC) {
            $name = 'formStatic';
        }

        $helper = $this->getView()->plugin($name);

        if ($helper instanceof Translator\TranslatorAwareInterface) {
            $rollbackTextDomain = $this->getTranslatorTextDomain();
            $helper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        if ($element instanceof FieldsetInterface) {
            $markup = call_user_func_array($helper, array_slice(func_get_args(), 1));
        } else {
            $markup = $helper($element);
        }

        if (isset($rollbackTextDomain)) {
            $helper->setTranslatorTextDomain($rollbackTextDomain);
        }

        return $markup;
    }

    /**
     * {@inheritDoc}
     */
    protected function renderInstance(ElementInterface $element)
    {
        $args = func_get_args();

        foreach ($this->classMap as $class => $pluginName) {
            if ($element instanceof $class) {
                array_unshift($args, $pluginName);

                return call_user_func_array([$this, 'renderHelper'], $args);
            }
        }

        return;
    }

    /**
     * {@inheritDoc}
     */
    protected function renderType(ElementInterface $element)
    {
        $type = $element->getAttribute('type');

        if (isset($this->typeMap[$type])) {
            $args = func_get_args();
            array_unshift($args, $this->typeMap[$type]);

            return call_user_func_array([$this, 'renderHelper'], $args);
        }

        return;
    }

    /**
     * {@inheritDoc}
     */
    public function addClass($class, $plugin)
    {
        $classMap = array_reverse($this->classMap, true);
        $classMap[$class] = $plugin;
        $this->classMap = array_reverse($classMap, true);

        return $this;
    }
}
