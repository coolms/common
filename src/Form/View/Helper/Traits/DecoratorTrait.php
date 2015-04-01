<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper\Traits;

use Zend\Form\ElementInterface,
    Zend\Form\FormInterface;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait DecoratorTrait
{
    use ElementOptionsTrait;

    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * @param string $markup
     * @param ElementInterface $element
     * @param FormInterface $form
     * @return string
     */
    protected function decorate($markup, ElementInterface $element, FormInterface $form)
    {
        $this->resetCounter();

        if ($element->getAttribute('type') === 'hidden') {
        	return $markup;
        }

        if ($markup !== '') {
            foreach ($this->getDecorators($element) as $decorator => $attribs) {
                if ($attribs === false) {
                    continue;
                }
                if (is_int($decorator)) {
                    $decorator = $attribs;
                    $attribs = [];
                }
                if (!in_array($decorator, $this->getAllowedDecorators())) {
                    continue;
                }
                $markup = $this->renderDecorator($markup, $decorator, $attribs, $element, $form);
                $this->counter++;
            }
        }

        return $markup;
    }

    /**
     * @param string $markup
     * @param string $decorator
     * @param array $attribs
     * @param ElementInterface $element
     * @param FormInterface $form
     * @return string
     */
    protected function renderDecorator($markup, $decorator, array $attribs, ElementInterface $element, FormInterface $form)
    {
        $plugin = $this->getView()->plugin($decorator);
        return $plugin((string) $markup, $attribs, $element, $form);
    }

    /**
     * @param ElementInterface $element
     * @return array
     */
    public function getDecorators(ElementInterface $element)
    {
        return (array) $this->getOption('decorators', $element);
    }

    /**
     * @param string $decorator
     * @param ElementInterface $element
     * @return array|null
     */
    public function getDecorator($decorator, ElementInterface $element)
    {
        $decorators = $this->getDecorators($element);
        if (isset($decorators[$decorator])) {
            return $decorators[$decorator];
        }
        if (in_array($decorator, $decorators)) {
            return [];
        }
    }

    /**
     * @param array $decorators
     * @return self
     */
    public function setAllowedDecorators(array $decorators)
    {
        if (property_exists($this, 'allowedDecorators')) {
            $this->allowedDecorators = $decorators;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedDecorators()
    {
        if (property_exists($this, 'allowedDecorators')) {
            return (array) $this->allowedDecorators;
        }

        return [];
    }

    /**
     * @return self
     */
    protected function resetCounter()
    {
        $this->counter = 0;
        return $this;
    }

    /**
     * @return number
     */
    protected function getCounter()
    {
        return (int) $this->counter;
    }
}
