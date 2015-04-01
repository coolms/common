<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Persistence;

use Zend\ServiceManager\ConfigInterface,
    Zend\Stdlib\InitializableInterface,
    CmsCommon\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for mappers.
 *
 * Enforces that mappers retrieved are instances of MapperInterface.
 */
class MapperPluginManager extends AbstractPluginManager
{
    /**
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);

        $this->addInitializer([$this, 'callMapperInit'], false);
    }

    /**
     * Call init() on any mapper that implements InitializableInterface
     *
     * @internal param $mapper
     */
    public function callMapperInit($mapper)
    {
        if ($mapper instanceof InitializableInterface) {
            $mapper->init();
        }
    }

    /**
     * Validate the plugin
     *
     * Checks that the mapper is an instance of MapperInterface
     *
     * @param  mixed $plugin
     * @throws \InvalidArgumentException
     * @return void
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof MapperInterface) {
            return; // we're okay
        }

        throw new \InvalidArgumentException(sprintf(
            'Can\'t create mapper for %s; Mapper must implement CmsCommon\Persistence\MapperInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
