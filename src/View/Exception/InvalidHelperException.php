<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Exception;

use Zend\View\Exception\InvalidHelperException as BaseInvalidHelperException;

/**
 * Invalid helper exception for CmsCommon\View
 */
class InvalidHelperException extends BaseInvalidHelperException implements ExceptionInterface
{
    /**
     * @param mixed $helper
     * @return self
     */
    public static function invalidHelperInstance($helper)
    {
        $callers = debug_backtrace();
        return new self(
            sprintf(
                'Invalid helper set in %s. '
                    . 'The helper must be an instance of Zend\View\Helper\AbstractHelper; %s given.',
                "{$callers[1]['class']}::{$callers[1]['function']}()",
                is_object($helper)
                    ? get_class($helper)
                    : gettype($helper)
            )
        );
    }
}
