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
 * Trait for the model to have a content
 * 
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait ContentableTrait
{
    /**
     * @var string
     * 
     * @Form\Type("Textarea")
     * @Form\Filter({"name":"StringTrim"})
     * @Form\Required(true)
     * @Form\Validator({
     *      "name":"StringLength",
     *      "options":{
     *          "encoding":"UTF-8",
     *          "min":0,
     *          "max":4294967295,
     *          "break_chain_on_failure":true,
     *      }})
     * @Form\Attributes({"required":true})
     * @Form\Options({
     *      "label":"Content",
     *      "text_domain":"default",
     *      })
     */
    protected $content;

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
