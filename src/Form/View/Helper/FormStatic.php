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

use Zend\Form\ElementInterface as Element;
use Zend\Form\View\Helper\FormInput;

class FormStatic extends FormInput
{
    /**
     * {@inheritDoc}
     */
    public function render(Element $element)
    {
        if ($element->getAttribute('type') === 'hidden') {
            return '';
        }

        if (method_exists($element, '__toString')) {
            $value = $element;
        } elseif (($value = $element->getValue()) instanceof \DateTime) {
            /* @todo Localization */
            $value = $value->format('m-d-Y H:i:s');
        }

        $attributes = $element->getAttributes();

        return sprintf('<p %s', $this->createAttributesString($attributes)) . '>'
            . $value
            . '</p>';
    }
}
