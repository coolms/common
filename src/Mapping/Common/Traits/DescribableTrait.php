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
 * Trait for the model to have a description
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait DescribableTrait
{
    /**
     * @var string
     *
     * @Form\Type("Text")
     * @Form\Filter({"name":"StripTags"})
     * @Form\Filter({"name":"StringTrim"})
     * @Form\Filter({"name":"Null"})
     * @Form\Required(false)
     * @Form\Validator({
     *      "name":"StringLength",
     *      "options":{
     *          "encoding":"UTF-8",
     *          "min":0,
     *          "max":255,
     *          "break_chain_on_failure":true,
     *      }})
     * @Form\Options({
     *      "label":"Description",
     *      "text_domain":"default"})
     */
    protected $description;

    /**
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
