<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Factory;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsCommon\Mvc\Controller\Plugin\TranslatePlural;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class TranslatePluralControllerPluginFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return TranslatePlural
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $services = $serviceLocator->getServiceLocator();
        /* @var $translator \Zend\Mvc\I18n\Translator */
        $translator = $services->get('MvcTranslator');

        return new TranslatePlural($translator);
    }
}
