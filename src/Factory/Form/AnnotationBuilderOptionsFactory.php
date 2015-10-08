<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Factory\Form;

use Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsCommon\Form\Options\FormAnnotationBuilder;

/**
 * Factory for {@see FormAnnotationBuilder}
 */
class AnnotationBuilderOptionsFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return FormAnnotationBuilder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        return new FormAnnotationBuilder(
            isset($config['form_annotation_builder'])
                ? $config['form_annotation_builder']
                : null
            );
    }
}
