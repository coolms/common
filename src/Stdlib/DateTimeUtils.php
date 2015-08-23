<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Stdlib;

use DateTime;

/**
 * Utility class for testing and manipulation of dates.
 *
 * Declared abstract, as we have no need for instantiation.
 */
abstract class DateTimeUtils
{
    /**
     * @param string|int|DateTime $date
     * @return DateTime
     */
    public static function normalize($date)
    {
        if ($date instanceof DateTime) {
            return $date;
        }

        if (is_int($date)) {
            $date = "@$date";
        }

        if (is_string($date)) {
            return new DateTime($date);
        }
    }
}
