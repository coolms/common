<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Listener;

use Zend\EventManager\AbstractListenerAggregate,
    Zend\EventManager\EventManagerInterface,
    Zend\Mvc\MvcEvent;

/**
 * PHP settings event listener
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class PhpSettingsListener extends AbstractListenerAggregate
{
    /**
     * @var string
     */
    protected $configKey = 'php_settings';

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
    	$this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'onBootstrap'], PHP_INT_MAX);
    }

    /**
     * Event callback to be triggered on bootstrap
     *
     * @param MvcEvent $e
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        $config = $e->getApplication()->getServiceManager()->get('Config');
        if (!empty($config[$this->configKey])) {
            $phpSettings = (array) $config[$this->configKey];
            foreach($phpSettings as $key => $value) {
                @ini_set($key, $value);
            }
        }
    }
}
