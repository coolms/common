<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon;

use Zend\EventManager\EventInterface,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\BootstrapListenerInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\ModuleManager,
    Zend\Mvc\ModuleRouteListener;

class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface
{
    /**
     * @param ModuleManager $moduleManager
     */
    public function init(ModuleManager $moduleManager)
    {
        $sm = $moduleManager->getEvent()->getParam('ServiceManager');
        $serviceListener = $sm->get('ServiceListener');
        $serviceListener->addServiceManager(
            'MapperManager',
            'mappers',
            'CmsCommon\Persistence\MapperPluginProviderInterface',
            'getMapperConfig'
        );
        $serviceListener->addServiceManager(
            'DomainServiceManager',
            'domain_services',
            'CmsCommon\Service\DomainServicePluginProviderInterface',
            'getDomainServiceConfig'
        );
        $serviceListener->addServiceManager(
            'SessionContainerManager',
            'session_containers',
            'CmsCommon\Session\ContainerPluginProviderInterface',
            'getSessionContainerConfig'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/../autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function onBootstrap(EventInterface $e)
    {
        set_error_handler([__CLASS__, 'handlePhpErrors']);

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    /**
     * @static
     * @access public
     * @param int $type
     * @param string $message
     * @param string $file
     * @param int $line
     * @throws \Exception
     */
    public static function handlePhpErrors($type, $message, $file, $line)
    {
        if ($type & error_reporting()) {
            throw new \Exception("Error: $message in file $file at line $line");
        }
    }
}
