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
    Zend\View\Renderer\PhpRenderer,
    CmsCommon\View\AcceptStrategy;

class AcceptStrategyFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return AcceptStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AcceptStrategy(
            $serviceLocator->get(PhpRenderer::class),
            $serviceLocator->get('ViewJsonRenderer'),
            $serviceLocator->get('ViewFeedRenderer')
        );
    }
}
