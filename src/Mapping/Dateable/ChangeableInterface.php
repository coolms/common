<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mapping\Dateable;

/**
 * Interface for the model that might change
 * 
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface ChangeableInterface
{
    /**
     * @return \DateTime
     */
    public function getChangedAt();

    /**
     * @param \DateTime $changedAt
     */
    public function setChangedAt(\DateTime $changedAt);
}
