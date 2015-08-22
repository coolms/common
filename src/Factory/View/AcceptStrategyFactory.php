<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Factory\View;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsCommon\View\AcceptStrategy;

class AcceptStrategyFactory implements FactoryInterface
{
    /**
     * Create AcceptStrategy
     *
     * @param  ServiceLocatorInterface $services
     * @return AcceptStrategy
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return new AcceptStrategy(
            $services->get('Zend\\View\\Renderer\\PhpRenderer'),
            $services->get('ViewJsonRenderer'),
            $services->get('ViewFeedRenderer')
        );
    }
}
