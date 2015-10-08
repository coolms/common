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
    CmsCommon\Mvc\Controller\CrudController,
    CmsCommon\Mvc\Controller\Options\CrudControllerOptionsInterface,
    CmsCommon\Mvc\Controller\Options\CrudControllerOptions;

class CrudControllerAbstractServiceFactory extends AbstractControllerAbstractServiceFactory
{
    /**
     * @var string
     */
    protected $configKey = 'crud_controllers';

    /**
     * {@inheritDoc}
     *
     * @return CrudController
     */
    protected function createControllerWithName(ServiceLocatorInterface $services, $requestedName)
    {
        $options = $this->getOptions($requestedName, $services);

        $controllerType = $options->getControllerType() ?: CrudController::class;

        return new $controllerType(
            $services->get('DomainServiceManager')->get($requestedName),
            $options
        );
    }

    /**
     * {@inheritDoc}
     *
     * @return CrudControllerOptionsInterface
     */
    protected function getDefaultOptions(ServiceLocatorInterface $services)
    {
        $options = parent::getDefaultOptions($services);

        if (!$options instanceof CrudControllerOptionsInterface) {
            $optionsType = CrudControllerOptions::class;
            $options = new $optionsType($options ?: []);
        }

        return $options;
    }
}
