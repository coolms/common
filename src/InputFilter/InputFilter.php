<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\InputFilter;

use Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerAwareTrait,
    Zend\Filter\FilterChain,
    Zend\InputFilter\BaseInputFilter,
    Zend\InputFilter\Exception,
    Zend\InputFilter\InputFilter as ZendInputFilter,
    Zend\InputFilter\InputFilterInterface as ZendInputFilterInterface,
    Zend\InputFilter\InputInterface,
    Zend\ServiceManager\AbstractPluginManager,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Validator\ValidatorChain,
    CmsCommon\Filter\BindableClosure;

class InputFilter extends ZendInputFilter implements
        InputFilterInterface,
        ServiceLocatorAwareInterface,
        EventManagerAwareInterface
{
    use ServiceLocatorAwareTrait,
        EventManagerAwareTrait;

    /**
     * @var FilterChain
     */
    protected $filterChain;

    /**
     * @var ValidatorChain
     */
    protected $validatorChain;

    /**
     * {@inheritDoc}
     *
     * @param bool $recursive
     */
    public function add($input, $name = null, $recursive = false)
    {
        if (is_array($input)
            || ($input instanceof Traversable && !$input instanceof InputFilterInterface)
        ) {
            $factory = $this->getFactory();
            $input = $factory->createInput($input);
        }

        if (!$input instanceof InputInterface && !$input instanceof ZendInputFilterInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an instance of %s or %s as its first argument; received "%s"',
                __METHOD__,
                InputInterface::class,
                ZendInputFilterInterface::class,
                (is_object($input) ? get_class($input) : gettype($input))
            ));
        }

        if ($input instanceof InputInterface && (empty($name) || is_int($name))) {
            $name = $input->getName();
        }

        if (isset($this->inputs[$name]) &&
            ($this->inputs[$name] instanceof InputInterface
                || ($recursive && $this->inputs[$name] instanceof ZendInputFilterInterface))
        ) {
            // The element already exists, so merge the config. Please note
            // that this merges the new input into the original.
            $original = $this->inputs[$name];
            if ($original !== $input) {
                $original->merge($input, $recursive);
            }
        } else {
            $this->inputs[$name] = $input;
        }

        if ($input instanceof InputInterface || $input instanceof InputFilterInterface) {
            $filter = $input->getFilterChain();
            $this->bindClosureFilter($filter);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function replace($input, $name)
    {
        parent::replace($input, $name);

        $input = $this->inputs[$name];
        if ($input instanceof InputInterface || $input instanceof InputFilterInterface) {
            $filter = $input->getFilterChain();
            $this->bindClosureFilter($filter);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid($context = null)
    {
        $valid = parent::isValid($context);

        if (!$valid) {
            return $valid;
        }

        $validators = $this->getValidatorChain();
        $valid = $validators->isValid(new \ArrayObject($this->getValues()), $context);

        if (!$valid) {
            $validators->getMessages();
        }

        return $valid;
    }

    /**
     * {@inheritDoc}
     */
    public function getValues()
    {
        $values = parent::getValues();
        $filter = $this->getFilterChain();
        $this->bindClosureFilter($filter);
        return $filter->filter($values);
    }

    /**
     * @param FilterChain $filterChain
     * @return void
     */
    private function bindClosureFilter(FilterChain $filterChain)
    {
        foreach ($filterChain->getFilters() as $filter) {
            if ($filter instanceof FilterChain) {
                $this->bindClosureFilter($filter);
                continue;
            }

            if ($filter instanceof BindableClosure) {
                $callback = $filter->getCallback();
                $callback = $callback->bindTo($this, $this);
                $filter->setCallback($callback);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getMessages()
    {
        $validatorMessages = $this->getValidatorChain()->getMessages();
        $messages = $validatorMessages ? array_values($validatorMessages) : [];

        foreach ($this->getInvalidInput() as $name => $input) {
            $messages[$name] = $input->getMessages();
        }

        return $messages;
    }

    /**
     * @return FilterChain
     */
    public function getFilterChain()
    {
        if (!$this->filterChain) {
            $filter = new FilterChain();
            $inputs = $this->getServiceLocator();
            if ($inputs instanceof AbstractPluginManager) {
                $services = $inputs->getServiceLocator();
                if ($services instanceof ServiceLocatorInterface && $services->has('FilterManager')) {
                    $filter->setPluginManager($services->get('FilterManager'));
                }
            }

            $this->setFilterChain($filter);
        }

        return $this->filterChain;
    }

    /**
     * @param FilterChain $filterChain
     * @return self
     */
    public function setFilterChain(FilterChain $filterChain)
    {
        $this->filterChain = $filterChain;
        return $this;
    }

    /**
     * @return ValidatorChain
     */
    public function getValidatorChain()
    {
        if (!$this->validatorChain) {
            $validator = new ValidatorChain();
            $inputs = $this->getServiceLocator();
            if ($inputs instanceof AbstractPluginManager) {
                $services = $inputs->getServiceLocator();
                if ($services instanceof ServiceLocatorInterface && $services->has('ValidatorManager')) {
                    $validator->setPluginManager($services->get('ValidatorManager'));
                }
            }

            $this->setValidatorChain($validator);
        }

        return $this->validatorChain;
    }

    /**
     * @param ValidatorChain $validatorChain
     * @return self
     */
    public function setValidatorChain(ValidatorChain $validatorChain)
    {
        $this->validatorChain = $validatorChain;
        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param bool $recursive
     */
    public function merge(BaseInputFilter $inputFilter, $recursive = false)
    {
        foreach ($inputFilter->getInputs() as $name => $input) {
            $this->add($input, $name, $recursive);
        }

        if ($inputFilter instanceof InputFilterInterface) {
            $this->getFilterChain()->merge($inputFilter->getFilterChain());
            $this->getValidatorChain()->merge($inputFilter->getValidatorChain());
        }

        return $this;
    }
}
