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
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface ObjectableInterface
{
    /**
     * @return object
     */
    public function getObject();

    /**
     * @param object $object
     * @return self
     */
    public function setObject($object);
}
