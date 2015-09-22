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
 * Trait for the model to have a title
 * 
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait TitleableTrait
{
    /**
     * @var string
     * 
     * @Form\Type("Text")
     * @Form\Filter({"name":"StringTrim"})
     * @Form\Required(true)
     * @Form\Validator({
     *      "name":"StringLength",
     *      "options":{
     *          "encoding":"UTF-8",
     *          "min":0,
     *          "max":255,
     *          "break_chain_on_failure":true,
     *      }})
     * @Form\Attributes({"required":true})
     * @Form\Options({
     *      "label":"Title",
     *      "text_domain":"default",
     *      })
     */
    protected $title;

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
