<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Event;

use Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    Zend\Mvc\ModuleRouteListener,
    Zend\Mvc\MvcEvent,
    Zend\Stdlib\AbstractOptions;

/**
 * Module options event listener
 *
 * Overrides module options from matched route/
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class ModuleOptionsListener extends AbstractListenerAggregate
{
    const OPTIONS_KEY           = 'module_options';
    const OPTIONS_CONFIG_KEY    = 'module_options_suffixes';

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH,
            [$this, 'loadModuleOptionsFromRoute'], PHP_INT_MAX);
    	$this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR,
    	    [$this, 'loadModuleOptionsFromRoute'], PHP_INT_MAX);
    	$this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH,
    	    [$this, 'setupModuleOptionsEventParam'], round(PHP_INT_MAX/2));
    }

    /**
     * Event callback to be triggered on dispatch(.error)
     *
     * @param MvcEvent $e
     * @return void
     */
    public function loadModuleOptionsFromRoute(MvcEvent $e)
    {
        if (!($matchRoute = $e->getRouteMatch())) {
            return;
        }

        $params = $matchRoute->getParams();
        if (!empty($params[static::OPTIONS_KEY]) && is_array($params[static::OPTIONS_KEY])) {
            $services = $e->getApplication()->getServiceManager();
            foreach ($params[static::OPTIONS_KEY] as $service => $options) {
                if ((class_exists($service) || interface_exists($service)) && $services->has($service)) {
                    $optionsService = $services->get($service);
                    if ($optionsService instanceof AbstractOptions) {
                        $optionsService->setFromArray($options);
                    }
                }
            }
        }
    }

    /**
     * Event callback to be triggered on dispatch
     *
     * @param MvcEvent $e
     * @return void
     */
    public function setupModuleOptionsEventParam(MvcEvent $e)
    {
        $services = $e->getApplication()->getServiceManager();
        $config = $services->get('Config');
        if (empty($config[static::OPTIONS_CONFIG_KEY])) {
            return;
        }

        $routeMatch = $e->getRouteMatch();
        $moduleNamespace = $routeMatch->getParam(
            ModuleRouteListener::MODULE_NAMESPACE,
            $routeMatch->getParam('controller')
        );

        $module = strstr($moduleNamespace, '\\', true);

        foreach ($config[static::OPTIONS_CONFIG_KEY] as $suffix) {
            $name = "$module\\$suffix";
            if ((class_exists($name) || interface_exists($name)) && $services->has($name)) {
                $moduleOptions = $services->get($name);
                if ($moduleOptions instanceof AbstractOptions) {
                    //set module options as MvcEvent param
                    $e->setParam('module-options', $moduleOptions);
                }

                break;
            }
        }
    }
}
