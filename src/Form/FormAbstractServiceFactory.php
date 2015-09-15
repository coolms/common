<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form;

use Zend\Form\Factory as FormFactory,
    Zend\Form\FormAbstractServiceFactory as ZendFormAbstractServiceFactory,
    Zend\ServiceManager\ServiceLocatorInterface;

class FormAbstractServiceFactory extends ZendFormAbstractServiceFactory
{
    /**
     * {@inheritDoc}
     */
    protected function getFormFactory(ServiceLocatorInterface $services)
    {
        if ($this->factory instanceof FormFactory) {
            return $this->factory;
        }

        $elements = null;
        if ($services->has('FormElementManager')) {
            $elements = $services->get('FormElementManager');
        }

        $this->factory = new Factory($elements);
        return $this->factory;
    }

    /**
     * {@inheritDoc}
     */
    protected function marshalInputFilter(array &$config, ServiceLocatorInterface $services, FormFactory $formFactory)
    {
        if ($services->has('InputFilterManager')) {
            $inputFilterFactory = $formFactory->getInputFilterFactory();
            $inputFilterFactory->setInputFilterManager($services->get('InputFilterManager'));
        }

        parent::marshalInputFilter($config, $services, $formFactory);
    }
}