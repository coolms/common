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

use Zend\Form\FormAbstractServiceFactory as ZendFormAbstractServiceFactory,
    Zend\ServiceManager\AbstractPluginManager,
    Zend\ServiceManager\MutableCreationOptionsInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Stdlib\ArrayUtils;

class FormAbstractServiceFactory extends ZendFormAbstractServiceFactory implements MutableCreationOptionsInterface
{
    /**
     * @var AnnotationBuilder
     */
    protected $annotationBuilder;

    /**
     * @var string
     */
    protected $cacheConfigKey = 'form_annotation_builder_cache';

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
            throw new \BadMethodCallException('This abstract factory is meant to be used only with a plugin manager');
        }

        $services = $formElements->getServiceLocator();

        $config = $this->getConfig($services);
        if (empty($config)) {
            return false;
        }

        return (isset($config[$requestedName])
            && is_array($config[$requestedName])
            && class_exists($requestedName, true));
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $formElements, $name, $requestedName)
    {
        if (!$this->canCreateServiceWithName($formElements, $name, $requestedName)) {
            throw new \BadMethodCallException('This abstract factory can\'t create form for "' . $requestedName . '"');
        }

        $services   = $formElements->getServiceLocator();
        $builder    = $this->getAnnotationBuilder($services);
        $formSpec   = ArrayUtils::iteratorToArray($builder->getFormSpecification($requestedName));
        $config     = $this->getConfig($services);

        $configOptions = [];
        if (!empty($config[$requestedName])) {
            $configOptions = $config[$requestedName];
        }

        $this->config[$requestedName] = array_replace_recursive($formSpec, $configOptions, $this->creationOptions);
        return parent::createServiceWithName($services, $name, $requestedName);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
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
        if (null === $this->annotationBuilder) {
            $factory = $this->getFormFactory($services);
            $this->annotationBuilder = new AnnotationBuilder($this->getAnnotationBuilderCache($services));
            $this->annotationBuilder->setFormFactory($factory);
        }

        return $this->annotationBuilder;
    }

    /**
     * @param ServiceLocatorInterface $services
     * @return \Zend\Cache\Storage\StorageInterface|null
     */
    protected function getAnnotationBuilderCache(ServiceLocatorInterface $services)
    {
        return $services->has($this->cacheConfigKey) ? $services->get($this->cacheConfigKey) : null;
    }
}
