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
    Zend\Form\Element\MonthSelect as MonthSelectElement,
    Zend\Form\Exception,
    Zend\Form\View\Helper\FormMonthSelect as ZendFormMonthSelect;

class FormMonthSelect extends ZendFormMonthSelect
{
    /**
     * {@inheritDoc}
     */
    public function render(ElementInterface $element)
    {
        if (!$element instanceof MonthSelectElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Zend\Form\Element\MonthSelect',
                __METHOD__
            ));
        }

        $elementAttribs = $element->getAttributes();
        unset($elementAttribs['name']);

        $id = $this->getId($element);

        $attribs = $element->getYearAttributes();
        $element->setYearAttributes(array_replace_recursive(
            $elementAttribs,
            empty($attribs['id']) ? ['id' => (empty($elementAttribs['id']) ? $id : $elementAttribs['id']) . '-year' ] : [],
            $attribs
        ));

        $attribs = $element->getMonthAttributes();
        $element->setMonthAttributes(array_replace_recursive(
            $elementAttribs,
            empty($attribs['id']) ? ['id' => (empty($elementAttribs['id']) ? $id : $elementAttribs['id']) . '-month' ] : [],
            $attribs
        ));

        return parent::render($element);
    }
}
