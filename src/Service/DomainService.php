<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Service;

use Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerAwareTrait,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\ServiceManager\ServiceManager,
    Zend\Stdlib\ArrayUtils,
    Zend\Stdlib\InitializableInterface,
    CmsCommon\Form\FormProviderTrait,
    CmsCommon\Persistence\MapperInterface,
    CmsCommon\Persistence\MapperProviderTrait,
    CmsCommon\Session\ContainerProviderTrait;

/**
 * Domain service
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
     */
    public function __construct($options, ServiceManager $manager)
    {
        $this->setServiceLocator($manager);

        if (is_string($options)) {
            $this->className = $options;
        } elseif ($options instanceof MapperInterface) {
            $this->setMapper($options);
            $this->className = (string) $options->getClassName();
        } elseif ($options instanceof \Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (is_array($options)) {
            if (empty($options['class_name'])) {
                throw new \InvalidArgumentException(sprintf(
                    'Option missing; $options must contain \'class_name\' option'
                ));
            }

            $this->className = (string) $options['class_name'];
            unset($options['class_name']);

            $this->setOptions($options);
        }

        if (!$this->className) {
            throw new \InvalidArgumentException(sprintf(
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
    public function hasForm()
    {
        $sm = $this->getServiceLocator();
        return $sm->getServiceLocator()->get($this->formElementManager)->has($this->className);
    }

    /**
     * {@inheritDoc}
     */
    public function getForm()
    {
        if (null === $this->form && $this->hasForm()) {
            $sm = $this->getServiceLocator();
            $form = $sm->getServiceLocator()->get($this->formElementManager)->get($this->className, $this->options);
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
            $sm = $this->getServiceLocator();
            $mapper = $sm->getServiceLocator()->get($this->mapperManager)->get($this->className);
            $this->setMapper($mapper);
        }

        return $this->mapper;
    }

    /**
     * {@inheritDoc}
     */
    public function hasSessionContainer()
    {
        $sm = $this->getServiceLocator();
        return $sm->getServiceLocator()->get($this->sessionContainerManager)->has($this->className);
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionContainer()
    {
        if (null === $this->sessionContainer && $this->hasSessionContainer()) {
            $sm = $this->getServiceLocator();
            $container = $sm->get($this->sessionContainerManager)->get($this->className);
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
     * {@inheritDoc}
     */
    public function hydrate($data, $object = null)
    {
        if ($data instanceof \Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }
        if (!is_array($data)) {
            return $data;
        }

        $form = $this->getForm();
        if (null !== $object) {
            $form->setObject($object);
        }

        if ($form->setData($data)->isValid()) {
            return $form->getObject();
        }
    }
}
