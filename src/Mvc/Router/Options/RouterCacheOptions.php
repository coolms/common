<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mvc\Router\Options;

use Zend\Stdlib\AbstractOptions;

class RouterCacheOptions extends AbstractOptions implements RouterCacheOptionsInterface
{
    /**
     * @var bool
     */
    protected $__strictMode__ = false;

    /**
     * @var Cache service name
     */
    protected $cache = 'array';

    /**
     * {@inheritDoc}
     */
    public function setCache($name)
    {
        $this->cache = $name;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCache()
    {
        return $this->cache;
    }
}
