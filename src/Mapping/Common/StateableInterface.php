<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mapping\Common;

/**
 * Stateable interface
 * 
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface StateableInterface
{
    /**
     * Set state
     * 
     * @param mixed $state
     */
    public function setState($state);

    /**
     * Get state
     * 
     * @return mixed
     */
    public function getState();
}
