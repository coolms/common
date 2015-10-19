<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mapping\Common\Traits;

/**
 * Trait for the model to have a quantity
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait QuantifiableTrait
{
    /**
     * @var int
     *
     * @Form\Type("Number")
     * @Form\Filter({"name":"StringTrim"})
     * @Form\Filter({"name":"Int"})
     * @Form\Required(true)
     * @Form\Validator({"name":"Digits"})
     * @Form\Options({
     *      "label":"Quantity",
     *      "text_domain":"default"})
     */
    protected $quantity = 0;

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
