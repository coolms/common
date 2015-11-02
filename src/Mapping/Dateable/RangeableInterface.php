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
 * Interface for the model with date range
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface RangeableInterface
{
    /**
     * @return DateTime
     */
    public function getStartDate();

    /**
     * @param int|string|DateTime $date
     * @return self
     */
    public function setStartDate($date);

    /**
     * @return DateTime
     */
    public function getEndDate();

    /**
     * @param int|string|DateTime $date
     * @return self
    */
    public function setEndDate($date);
}
