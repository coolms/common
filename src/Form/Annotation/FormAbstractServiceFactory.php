<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\Annotation;

use Zend\ServiceManager\AbstractPluginManager,
    Zend\ServiceManager\MutableCreationOptionsInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    CmsCommon\Form\FormAbstractServiceFactory as CommonFormAbstractServiceFactory,
    CmsCommon\Stdlib\ArrayUtils;

class FormAbstractServiceFactory extends CommonFormAbstractServiceFactory implements MutableCreationOptionsInterface
{
    /**
     * @var AnnotationBuilder
     */
    protected $annotationBuilder;

    /**
     * @var string Top-level configuration key indicating forms configuration
     */
    protected $configKey = 'annotation_forms';

    /**
     * @var array
     */
    protected $creationOptions = [];

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $formElements, $name, $requestedName)
    {
        if (!$formElements instanceof AbstractPluginManager) {
            throw new \BadMethodCallException(
                'This abstract factory is meant to be used only with a plugin manager'
            );
        }

        $services = $formElements->getServiceLocator();

        $config = $this->getConfig($services);
        if (empty($config)) {
            return false;
        }

        return (isset($config[$requestedName])
            && is_array($config[$requestedName])
            && class_exists($requestedName));
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $formElements, $name, $requestedName)
    {
        if (!$this->canCreateServiceWithName($formElements, $name, $requestedName)) {
            throw new \BadMethodCallException(sprintf(
                'This abstract factory can\'t create form for %s',
                $requestedName
            ));
        }

        $services   = $formElements->getServiceLocator();
        $builder    = $this->getAnnotationBuilder($services);
        $formSpec   = ArrayUtils::iteratorToArray($builder->getFormSpecification($requestedName));
        $config     = $this->getConfig($services);

        foreach ($config as $name => $spec) {
            if (interface_exists($name) && $requestedName instanceof $name) {
                $formSpec = array_replace_recursive($formSpec, $spec);
            }
        }

        if (!empty($config[$requestedName])) {
            $formSpec = array_replace_recursive($formSpec, (array) $config[$requestedName]);
        }

        if (isset($formSpec['options'])) {
            $formSpec['options'] = array_replace_recursive($formSpec['options'], $this->creationOptions);
        } else {
            $formSpec['options'] = $this->creationOptions;
        }

        // Setting up some defaults
        if (!isset($formSpec['options']['merge_input_filter'])) {
            $formSpec['options']['merge_input_filter'] = true;
        }
        if (!isset($formSpec['options']['prefer_form_input_filter'])) {
            $formSpec['options']['prefer_form_input_filter'] = true;
        }
        if (!isset($formSpec['options']['use_input_filter_defaults'])) {
            $formSpec['options']['use_input_filter_defaults'] = true;
        }

        $this->config[$requestedName] = $formSpec;

        return parent::createServiceWithName($services, $name, $requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = ArrayUtils::filterRecursive($options, null, true);
        return $this;
    }

    /**
     * Get annotation builder
     *
     * @param ServiceLocatorInterface $services
     * @return AnnotationBuilder
     */
    protected function getAnnotationBuilder(ServiceLocatorInterface $services)
    {
        return $services->get('FormAnnotationBuilder');
    }

    /**
     * {@inheritDoc}
     */
    protected function getFormFactory(ServiceLocatorInterface $services)
    {
        $annotationBuilder = $this->getAnnotationBuilder($services);
        if ($services->has('FormElementManager')) {
            $formElementManager = $services->get('FormElementManager');
            $formElementManager->injectFactory($annotationBuilder);
        }

        return $annotationBuilder->getFormFactory();
    }
}
