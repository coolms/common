<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\Element;

use Zend\Form\Element,
    Zend\Form\Exception;

class StaticElement extends Element
{
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = [
        'type' => 'static',
    ];

    /**
     * {@inheritDoc}
     * @throws Exception\BadMethodCallException
     */
    public function setValue($value)
    {
        if ($this->value !== null && $value !== null) {
            throw new Exception\BadMethodCallException("Can't set value for static form element");
        }

        return parent::setValue($value);
    }
}
