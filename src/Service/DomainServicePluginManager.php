<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Service;

use Zend\ServiceManager\ConfigInterface,
    Zend\Stdlib\InitializableInterface,
    CmsCommon\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for domain services.
 *
 * Enforces that services retrieved are instances of ServiceInterface.
 */
class DomainServicePluginManager extends AbstractPluginManager
{
    /**
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addInitializer([$this, 'callServiceInit'], false);
    }

    /**
     * Call init() on any service that implements InitializableInterface
     *
     * @internal param $service
     */
    public function callServiceInit($service)
    {
        if ($service instanceof InitializableInterface) {
            $service->init();
        }
    }

    /**
     * Validate the plugin
     *
     * Checks that the service is an instance of ServiceInterface
     *
     * @param  mixed $plugin
     * @throws \InvalidArgumentException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof DomainServiceInterface) {
            return; // we're okay
        }

        throw new \InvalidArgumentException(sprintf(
            'Can\'t create domain service for %s; '
                . 'Domain Service must implement CmsCommon\Service\DomainServiceInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
