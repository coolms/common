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

use Traversable,
    CmsCommon\Stdlib\ArrayUtils;

class ArrayOrTraversable extends AbstractHelper
{
    /**
     * @var string
     */
    protected $instanceType = Traversable::class;

    /**
     * @var string
     */
    protected $type = 'array';

    /**
     * @var string
     */
    protected $glue = ', ';

    /**
     * {@inheritDoc}
     */
    protected function format($value, $glue = null)
    {
        if ($value instanceof Traversable) {
            $value = ArrayUtils::iteratorToArray($iterator, false);
        }

        $value = array_map('strval', $value);
        return implode($glue ?: $this->glue, $value);
    }
}
