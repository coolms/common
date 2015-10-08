<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Factory\View\Helper;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsCommon\View\Helper\Cookie;

class CookieFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return Cookie
     */
    public function createService(ServiceLocatorInterface $viewHelpers)
    {
        $services = $viewHelpers->getServiceLocator();
        $app = $services->get('Application');
        return new Cookie($app->getRequest());
    }
}
