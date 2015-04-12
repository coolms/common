<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Cache;

use Zend\Cache\Storage\StorageInterface;

interface StorageProviderInterface
{
    /**
     * @param StorageInterface $cacheStorage
     * @return self
     */
    public function setCacheStorage(StorageInterface $cacheStorage);

    /**
     * @return StorageInterface
     */
    public function getCacheStorage();
}
