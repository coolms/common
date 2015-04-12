<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Service;

use Zend\ServiceManager\AbstractFactoryInterface,
    Zend\ServiceManager\AbstractPluginManager,
    Zend\ServiceManager\MutableCreationOptionsInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

class DomainServiceAbstractServiceFactory implements AbstractFactoryInterface, MutableCreationOptionsInterface
{
    /**
     * @var array
     */
    protected $creationOptions = [];

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $domainServices, $name, $requestedName)
    {
        if (!$domainServices instanceof AbstractPluginManager) {
            throw new \BadMethodCallException('Domain service abstract factory is meant to be used only with a plugin manager');
        }

        $services = $domainServices->getServiceLocator();

        return $services->get('MapperManager')->has($requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $domainServices, $name, $requestedName)
    {
        if (!$this->canCreateServiceWithName($domainServices, $name, $requestedName)) {
            throw new \BadMethodCallException('Domain service abstract factory can\'t create service for "'
                . $requestedName . '"');
        }

        $this->creationOptions['class_name'] = $requestedName;
        return new DomainService($this->creationOptions, $domainServices);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}
