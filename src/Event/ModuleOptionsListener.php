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
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\Stdlib\AbstractOptions;

/**
 * Module options event listener
 *
 * Overrides module options from matched route/
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class ModuleOptionsListener extends AbstractListenerAggregate implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const OPTIONS_KEY = 'module_options';

    /**
     * @var string
     */
    protected $moduleOptionsSuffixesConfigKey = 'module_options_suffixes';

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH,       [$this, 'loadRouteModuleOptions'], PHP_INT_MAX);
    	$this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'loadRouteModuleOptions'], PHP_INT_MAX);
    	$this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH,       [$this, 'loadModuleOptions'], round(PHP_INT_MAX/2));
    	$this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH,       [$this, 'loadLayoutNamespace']);
    }

    /**
     * Event callback to be triggered on dispatch
     *
     * @param MvcEvent $e
     * @return void
     */
    public function loadRouteModuleOptions(MvcEvent $e)
    {
        if (!($matchRoute = $e->getRouteMatch())) {
            return;
        }

        $params = $matchRoute->getParams();
        if (!empty($params[static::OPTIONS_KEY]) && is_array($params[static::OPTIONS_KEY])) {
            $services = $this->getServiceLocator();
            foreach ($params[static::OPTIONS_KEY] as $service => $options) {
                if ($services->has($service)) {
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
    public function loadModuleOptions(MvcEvent $e)
    {
        $services = $this->getServiceLocator();
        $config = $services->get('Config');
        if (empty($config[$this->moduleOptionsSuffixesConfigKey])) {
            return;
        }

        $moduleNamespace = $this->getModuleNamespace($e);

        foreach ($config[$this->moduleOptionsSuffixesConfigKey] as $suffix) {
            if ($services->has($moduleNamespace . '\\' . $suffix)) {
                $moduleOptions = $services->get($moduleNamespace . '\\' . $suffix);
                if ($moduleOptions instanceof AbstractOptions) {
                    //set module options as MvcEvent param
                    $e->setParam('module-options', $moduleOptions);
                }

                break;
            }
        }
    }

    /**
     * Sets module namespace into layout
     *
     * @param MvcEvent $e
     * @return void
     */
    public function loadLayoutNamespace(MvcEvent $e)
    {
        $e->getTarget()->layout()->modulenamespace = $this->getModuleNamespace($e);
    }

    /**
     * @param MvcEvent $e
     * @return string
     */
    protected function getModuleNamespace(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();

        $moduleNamespace = $routeMatch->getParam(
            ModuleRouteListener::MODULE_NAMESPACE,
            $routeMatch->getParam('controller')
        );
        $moduleNamespace = strstr($moduleNamespace, '\\', true);

        return $moduleNamespace;
    }
}
