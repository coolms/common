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
    Zend\Form\FormInterface,
    Zend\View\Renderer\RendererInterface,
    CmsCommon\View\Helper\Decorator\Decorator,
    CmsCommon\View\Helper\Decorator\DecoratorProviderInterface;

trait DecoratorTrait
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
     * @param ElementInterface $element
     * @param FormInterface $form
     * @return array
     */
    protected function getDecorators(ElementInterface $element, FormInterface $form = null)
    {
        $decorators = (array) $element->getOption($this->getDecoratorNamespace());

        $helper = $this->getElementHelper();
        if ($helper instanceof DecoratorProviderInterface) {
            $decorators = array_replace_recursive(
                $helper->getDecoratorSpecification($element, $form),
                $decorators
            );
        }

        return $decorators;
    }

    /**
     * @return string
     */
    public function getDecoratorNamespace()
    {
        return $this->decoratorNamespace;
    }

    /**
     * @return RendererInterface
     */
    abstract public function getView();
}
