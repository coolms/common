<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon;

use Zend\Loader\ClassMapAutoloader,
    Zend\Loader\StandardAutoloader,
    Zend\EventManager\EventInterface,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\BootstrapListenerInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\InitProviderInterface,
    Zend\ModuleManager\ModuleManagerInterface,
    Zend\Mvc\ModuleRouteListener,
    Zend\Validator\AbstractValidator,
    CmsCommon\Persistence\MapperPluginProviderInterface,
    CmsCommon\Service\DomainServicePluginProviderInterface,
    CmsCommon\Session\ContainerPluginProviderInterface;

class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    InitProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        if (class_exists('ZendDeveloperTools\\Module')) {
            $moduleManager->loadModule('ZendDeveloperTools');
        }

        $moduleManager->loadModule('OcraCachedViewResolver');
        $moduleManager->loadModule('StrokerCache');
        $moduleManager->loadModule('CmsJquery');

        $sm = $moduleManager->getEvent()->getParam('ServiceManager');
        $serviceListener = $sm->get('ServiceListener');
        $serviceListener->addServiceManager(
            'MapperManager',
            'mappers',
            MapperPluginProviderInterface::class,
            'getMapperConfig'
        );
        $serviceListener->addServiceManager(
            'DomainServiceManager',
            'domain_services',
            DomainServicePluginProviderInterface::class,
            'getDomainServiceConfig'
        );
        $serviceListener->addServiceManager(
            'SessionContainerManager',
            'session_containers',
            ContainerPluginProviderInterface::class,
            'getSessionContainerConfig'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return [
            ClassMapAutoloader::class => [
                __DIR__ . '/../autoload_classmap.php',
            ],
            StandardAutoloader::class => [
                'fallback_autoloader' => true,
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

        $app = $e->getApplication();
        $eventManager = $app->getEventManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $serviceManager = $app->getServiceManager();

        if ($serviceManager->has('MvcTranslator')) {
            // Setting up default MVC translator
            $translator = $serviceManager->get('MvcTranslator');
            AbstractValidator::setDefaultTranslator($translator);
        }
    }

    /**
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
