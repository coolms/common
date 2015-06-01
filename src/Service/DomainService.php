<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Service;

use Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerAwareTrait,
    Zend\Form\FormInterface,
    Zend\ServiceManager\AbstractPluginManager,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\ServiceManager,
    Zend\Stdlib\ArrayUtils,
    Zend\Stdlib\InitializableInterface,
    CmsCommon\Form\FormProviderTrait,
    CmsCommon\Persistence\MapperInterface,
    CmsCommon\Persistence\MapperProviderTrait,
    CmsCommon\Service\Exception\InvalidArgumentException,
    CmsCommon\Service\Exception\RuntimeException,
    CmsCommon\Session\ContainerProviderTrait;

/**
 * Generic Domain Service
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 *
 * @method ServiceManager getServiceLocator()
 */
class DomainService implements DomainServiceInterface, EventManagerAwareInterface, InitializableInterface
{
    use ContainerProviderTrait,
        EventManagerAwareTrait,
        FormProviderTrait,
        MapperProviderTrait,
        ServiceLocatorAwareTrait;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $formElementManager = 'FormElementManager';

    /**
     * @var string
     */
    protected $mapperManager = 'MapperManager';

    /**
     * @var string
     */
    protected $sessionContainerManager = 'SessionContainerManager';

    /**
     * __construct
     *
     * @param string|array|\Traversable|MapperInterface $options
     * @param ServiceManager $services
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __construct($options, ServiceManager $manager)
    {
        $this->setServiceLocator($manager);

        if (is_string($options)) {
            $this->setClassName($options);
        } elseif ($options instanceof MapperInterface) {
            $this->setMapper($options);
            $this->setClassName($options->getClassName());
        } elseif ($options instanceof \Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (is_object($options) && method_exists($options, 'toArray')) {
            $options = $options->toArray();
        }

        if (is_array($options)) {
            if (!$this->getClassName()) {
                if (empty($options['class_name'])) {
                    throw new RuntimeException(sprintf(
                        'Option missing; $options must contain \'class_name\' option'
                    ));
                }

                $this->setClassName($options['class_name']);
                unset($options['class_name']);
            }

            $this->setOptions($options);
        }

        if (!$this->getClassName()) {
            throw new InvalidArgumentException(sprintf(
                'First argument passed to %s::%s must be a string, array, \Traversable '
                    . 'or an instance of CmsCommon\Persistence\Mapper\MapperInterface, %s given',
                __CLASS__,
                __METHOD__,
                is_object($options) ? get_class($options) : gettype($options)
            ));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function init() {}

    /**
     * {@inheritDoc}
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set domain entity class name
     *
     * @param string $className
     * @return self
     */
    protected function setClassName($className)
    {
        $this->className = (string) $className;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasForm()
    {
        $services = $this->getServiceManager();
        return $services->get($this->formElementManager)->has($this->className);
    }

    /**
     * {@inheritDoc}
     */
    public function getForm()
    {
        if (null === $this->form && $this->hasForm()) {
            $services = $this->getServiceManager();
            $form = $services->get($this->formElementManager)->get($this->className, $this->options);
            $this->setForm($form);
        }

        return $this->form;
    }

    /**
     * {@inheritDoc}
     */
    public function getMapper()
    {
        if (null === $this->mapper) {
            $services = $this->getServiceManager();
            $mapper = $services->get($this->mapperManager)->get($this->className);
            $this->setMapper($mapper);
        }

        return $this->mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function hasSessionContainer()
    {
        $services = $this->getServiceManager();
        return $services->get($this->sessionContainerManager)->has($this->className);
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionContainer()
    {
        if (null === $this->sessionContainer && $this->hasSessionContainer()) {
            $services = $this->getServiceManager();
            $container = $services->get($this->sessionContainerManager)->get($this->className);
            $this->setSessionContainer($container);
        }

        return $this->sessionContainer;
    }

    /**
     * @param array $options
     * @return self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }
    }

    /**
     * @param string $option
     * @param mixed $value
     * @return self
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($data, FormInterface $form = null, $object = null)
    {
        if ($data instanceof \Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }

        if (!is_array($data)) {
            return $data;
        }

        if (!$form) {
            $form = $this->getForm();
        }

        if (null !== $object) {
            $form->setObject($object);
        }

        if ($form->hasValidated()) {
            return $form->getObject();
        }

        if ($form->setData($data)->isValid()) {
            return $form->getObject();
        }
    }

    /**
     * @return ServiceLocatorInterface
     */
    protected function getServiceManager()
    {
        $serviceLocator = $this->getServiceLocator();
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $serviceLocator;
    }
}
