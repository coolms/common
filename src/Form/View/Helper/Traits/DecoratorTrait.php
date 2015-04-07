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
    Zend\Form\FormInterface,
    CmsCommon\View\Helper\Decorator;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait DecoratorTrait
{
    use OptionsTrait;

    /**
     * @param ElementInterface $element
     * @return array
     */
    public function getDecorators(ElementInterface $element)
    {
        return (array) $this->getOption(Decorator::OPTION_KEY, $element);
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
}
