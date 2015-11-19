<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper\Type;

class Object extends AbstractHelper
{
    /**
     * @var string
     */
    protected $type = 'object';

    /**
     * {@inheritDoc}
     */
    protected function format($value)
    {
        if (method_exists($value, '__toString')) {
            return (string) $value;
        }

        return '';
    }
}
