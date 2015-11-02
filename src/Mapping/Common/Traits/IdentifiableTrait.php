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
 * Trait for the model to have an identity
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait IdentifiableTrait
{
    /**
     * @var mixed
     *
     * @Form\Type("Text")
     * @Form\Filter({"name":"StripTags"})
     * @Form\Filter({"name":"StringTrim"})
     * @Form\Required(true)
     * @Form\AllowEmpty(true)
     * @Form\Options({
     *      "label":"Identity",
     *      "text_domain":"default"})
     */
    protected $id;

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
