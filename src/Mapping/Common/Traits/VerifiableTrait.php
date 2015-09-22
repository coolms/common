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
 * Trait for the model to be verifiable
 * 
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait VerifiableTrait
{
    /**
     * @var bool
     * 
     * @Form\Type("Checkbox")
     * @Form\Filter({"name":"Boolean"})
     * @Form\Required(false)
     * @Form\Options({
     *      "label":"Verified",
     *      "text_domain":"default",
     *      "checked_value":true,
     *      "unchecked_value":false,
     *      })
     */
    protected $verified = false;

    /**
     * @param bool $verified
     */
    public function setVerified($verified)
    {
        $this->verified = (bool) $verified;
    }

    /**
     * @return bool
     */
    public function getVerified()
    {
        return $this->verified;
    }
}
