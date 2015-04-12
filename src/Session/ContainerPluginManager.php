<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Session;

use Zend\Session\AbstractContainer,
    CmsCommon\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for doamin session containers.
 *
 * Enforces that containers retrieved are instances of AbstractContainer.
 */
class ContainerPluginManager extends AbstractPluginManager
{
    /**
     * Validate the plugin
     *
     * Checks that the container is an instance of AbstractContainer
     *
     * @param  mixed $plugin
     * @throws \InvalidArgumentException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AbstractContainer) {
            return; // we're okay
        }

        throw new \InvalidArgumentException(sprintf(
            'Can\'t create session container for %s; Session conatiner must implement Zend\Session\AbstractContainer',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
