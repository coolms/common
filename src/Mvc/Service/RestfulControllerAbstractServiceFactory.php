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

use Zend\ServiceManager\ServiceLocatorInterface,
    CmsCommon\Mvc\Controller\RestfulController,
    CmsCommon\Mvc\Controller\Options\RestfulControllerOptionsInterface,
    CmsCommon\Mvc\Controller\Options\RestfulControllerOptions;

class RestfulControllerAbstractServiceFactory extends AbstractControllerAbstractServiceFactory
{
    /**
     * @var string
     */
    protected $configKey = 'rest_controllers';

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $controllers, $name, $requestedName)
    {
        $services =  $controllers->getServiceLocator();

        return $this->getRequest($services)->isXmlHttpRequest()
            && parent::canCreateServiceWithName($controllers, $name, $requestedName);
    }

    /**
     * {@inheritDoc}
     *
     * @return RestfulController
     */
    protected function createControllerWithName(ServiceLocatorInterface $services, $requestedName)
    {
        $options = $this->getOptions($requestedName, $services);

        $controllerType = $options->getControllerType() ?: RestfulController::class;

        return new $controllerType(
            $services->get('DomainServiceManager')->get($requestedName),
            $options
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return RestfulControllerOptionsInterface
     */
    protected function getDefaultOptions(ServiceLocatorInterface $services)
    {
        $options = parent::getDefaultOptions($services);

        if (!$options instanceof RestfulControllerOptionsInterface) {
            $optionsType = RestfulControllerOptions::class;
            $options = new $optionsType($options ?: []);
        }

        return $options;
    }
}
