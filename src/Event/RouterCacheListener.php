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
    Zend\Http\Request as HttpRequest,
    Zend\Mvc\ModuleRouteListener,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Router\Http\Literal as LiteralRoute,
    Zend\Stdlib\AbstractOptions;

/**
 * Route cache event listener
 *
 * Caches 
 */
class RouterCacheListener extends AbstractListenerAggregate
{
    const EVENT_LOAD = MvcEvent::EVENT_ROUTE;
    const EVENT_LOAD_PRIORITY = 10;
    const EVENT_SAVE = MvcEvent::EVENT_ROUTE;
    const EVENT_SAVE_PRIORITY = 0;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(static::EVENT_LOAD, [$this, 'routeLoad'], static::EVENT_LOAD_PRIORITY);
        $this->listeners[] = $events->attach(static::EVENT_SAVE, [$this, 'routeSave'], static::EVENT_SAVE_PRIORITY);
    }

    /**
     * Method that tries to save a route match into a cache
     *
     * @param MvcEvent $event
     */
    public function routeSave(MvcEvent $event)
    {
        $match = $event->getRouteMatch();
        if (!$match || !$event->getRequest() instanceof HttpRequest) {
            return;
        }

        if ($event->getParam('route-cached') || !$event->getParam('route-cacheable')) {
            return;
        }

        $path = $event->getRequest()->getUri()->getPath();

        // save the route match into the cache.
        $services = $event->getApplication()->getServiceManager();

        $cacheName = $services->get('CmsCommon\\Mvc\\Router\\Options\\RouterCacheOptions')->getCache();
        $cache = $services->get($cacheName);

        $cacheKey = $this->getCacheKey($path);

        $name = $match->getMatchedRouteName();
        $data = [
            'name' => $name,
            'spec' => [
                'type' => 'Literal',
                'options' => [
                    'route' => $path,
                    'defaults' => $match->getParams(),
                ],
            ],
        ];

        $routes = $services->get('Config')['router']['routes'];
        if (isset($routes[$name])) {
            $data['spec'] = array_replace_recursive($routes[$name], $data['spec']);
        }

        $cache->setItem($cacheKey, $data);
    }

    /**
     * Method that tries to load cached routes and speed up the matching
     *
     * @param MvcEvent $event
     */
    public function routeLoad(MvcEvent $event)
    {
        $request = $event->getRequest();
        if(!(
            $request instanceof HttpRequest &&
            $request->isGet() &&
            $request->getQuery()->count() == 0
        )) {
            // We do not cache route match for requests that can produce
            // different match.
            return;
        }

        $event->setParam('route-cacheable', true);

        // check if we have data in our cache
        $path = $request->getUri()->getPath();

        $services = $event->getApplication()->getServiceManager();

        $cacheName = $services->get('CmsCommon\\Mvc\\Router\\Options\\RouterCacheOptions')->getCache();
        $cache = $services->get($cacheName);

        $cacheKey = $this->getCacheKey($path);
        $cachedData = $cache->getItem($cacheKey);

        if($cachedData) {
            $event->setParam('route-cached', true);
            $router = $event->getRouter();
            $router->addRoute($cachedData['name'], $cachedData['spec'], round(PHP_INT_MAX/2));
        }
    }

    /**
     * @param string $path
     * @return string
     */
    private function getCacheKey($path)
    {
        return 'route-' . md5($path);
    }
}
