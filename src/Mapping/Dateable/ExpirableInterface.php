<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mapping\Dateable;

use DateTime;

/**
 * Interface for the model that might expire
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface ExpirableInterface
{
    /**
     * Get expiration datetime
     *
     * @return DateTime
     */
    public function getExpireAt();

    /**
     * Set expiration datetime
     *
     * @param DateTime $expireAt
     * @return self
     */
    public function setExpireAt(DateTime $expireAt = null);
}
