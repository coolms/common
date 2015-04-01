<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mapping\Common\Traits;

/**
 * Trait for the model to have a different states
 * 
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait StateableTrait
{
    /**
     * @var mixed
     * 
     * @Form\Type("Select")
     * @Form\Attributes({"options":{}})
     * @Form\Options({
     *      "empty_option":"Select state",
     *      "label":"Select state",
     *      "translator_text_domain":"default",
     *      })
     */
    protected $state;

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }
}
