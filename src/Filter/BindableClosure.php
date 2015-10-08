<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Filter;

use Closure,
    Zend\Filter\Callback,
    Zend\Filter\Exception;

class BindableClosure extends Callback
{
    /**
     * Sets a new Closure for this filter
     *
     * @param  Closure $callback
     * @throws Exception\InvalidArgumentException
     * @return self
     */
    public function setCallback($callback)
    {
        if (!$callback instanceof Closure) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid parameter for callback: must be %s',
                Closure::class
            ));
        }

        return parent::setCallback($callback);
    }
}
