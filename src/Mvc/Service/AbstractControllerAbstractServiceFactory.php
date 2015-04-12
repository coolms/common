<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mvc\Service;

use Zend\Http\Request,
    Zend\Mvc\Controller\AbstractActionController,
    Zend\ServiceManager\AbstractFactoryInterface,
    Zend\ServiceManager\AbstractPluginManager,
    Zend\ServiceManager\MutableCreationOptionsInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsCommon\Mvc\Controller\ControllerOptionsInterface;

abstract class AbstractControllerAbstractServiceFactory implements
    AbstractFactoryInterface,
    MutableCreationOptionsInterface
{
    /**
     * @var string
     */
    protected $configKey;

    /**
     * @var array
     */
    protected $creationOptions = [];

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $controllers, $name, $requestedName)
    {
        if (!$controllers instanceof AbstractPluginManager) {
            throw new \BadMethodCallException('This abstract factory is meant to be used '
                . 'only with a plugin manager');
        }

        $services = $controllers->getServiceLocator();

        $request = $this->getRequest($services);
        if (!$request instanceof Request) {
            // This abstract controller factory can only handle HTTP requests
            return false;
        }

        $requestedName = $this->getCannonicalName($requestedName, $controllers->getCanonicalNames());
        return $services->get('DomainServiceManager')->has($requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $controllers, $name, $requestedName)
    {
        $requestedName = $this->getCannonicalName($requestedName, $controllers->getCanonicalNames());

        if (!$this->canCreateServiceWithName($controllers, $name, $requestedName)) {
            throw new \BadMethodCallException('This abstract factory can\'t create controller '
                . 'for "' . $requestedName . '"');
        }

        $services = $controllers->getServiceLocator();
        return $this->createControllerWithName($services, $requestedName);
    }

    /**
     * @param ServiceLocatorInterface $services
     * @param string $requestedName
     * @return AbstractActionController
     */
    abstract protected function createControllerWithName(ServiceLocatorInterface $services, $requestedName);

    /**
     * {@inheritDoc}
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }

    /**
     * @param ServiceLocatorInterface $services
     * @return ControllerOptionsInterface
     */
    protected function getDefaultOptions(ServiceLocatorInterface $services)
    {
        if ($services->has('Application')) {
            $options = $services->get('Application')->getMvcEvent()->getParam('module-options');
        }

        return $options;
    }

    /**
     * Retrieves configuration options
     *
     * @param string $requestedName
     * @param ServiceLocatorInterface $services
     * @return array
     */
    protected function getConfig($requestedName, ServiceLocatorInterface $services)
    {
        if ($this->configKey) {
            $config = $services->get('Config');
            if (!empty($config[$this->configKey][$requestedName])
                && is_array($config[$this->configKey][$requestedName])
            ) {
                return $config[$this->configKey][$requestedName];
            }
        }

        return [];
    }

    /**
     * @param string $requestedName
     * @param ServiceLocatorInterface $services
     * @return ControllerOptionsInterface
     */
    protected function getOptions($requestedName, ServiceLocatorInterface $services)
    {
        $options = $this->getDefaultOptions($services);
        $config  = $this->getConfig($requestedName, $services);

        if ($config) {
            $options->setFromArray($config);
        }

        if ($this->creationOptions) {
            $options->setFromArray($this->creationOptions);
        }

        return $options;
    }

    /**
     * @param string $requestedName
     * @param array $canonicalNames
     * @return string
     */
    protected function getCannonicalName($requestedName, array $canonicalNames)
    {
        if (!class_exists($requestedName)
            && ($keys = array_keys($canonicalNames, $requestedName))
        ) {
            return current($keys);
        }

        return $requestedName;
    }

    /**
     * @param ServiceLocatorInterface $services
     * @return void|Request
     */
    protected function getRequest(ServiceLocatorInterface $services)
    {
        if (!$services->has('Application')) {
            return;
        }

        return $services->get('Application')->getRequest();
    }
}
