<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper;

use Zend\View\Helper\AbstractHelper,
    CmsCommon\Stdlib\DateTimeUtils;

/**
 * View helper for rendering.
 */
class DateTime extends AbstractHelper
{
    /**
     * Returns new DateTime object
	 *
	 * @param mixed time[optional]
	 * @return \DateTime
     */
    public function __invoke($time = null)
    {
        if (null === $time) {
            $time = 'now';
        }

        return DateTimeUtils::normalize($time);
    }
}
