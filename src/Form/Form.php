<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form;

use Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerAwareTrait,
    Zend\Form\ElementInterface,
    Zend\Form\Element\Collection,
    Zend\Form\Exception\InvalidArgumentException,
    Zend\Form\FieldsetInterface,
    Zend\Form\Form as ZendForm,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\Stdlib\ArrayUtils;

class Form extends ZendForm implements
        CommonElementsInterface,
        EventManagerAwareInterface,
        ServiceLocatorAwareInterface
{
    use CommonElementsTrait,
        EventManagerAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * Set the validation group (set of values to validate)
     *
     * Typically, proxies to the composed input filter
     *
     * @throws InvalidArgumentException
     * @return self
     */
    public function setElementGroup()
    {
        $argc = func_num_args();
        if (0 === $argc) {
            throw new InvalidArgumentException(sprintf(
                '%s expects at least one argument; none provided',
                __METHOD__
            ));
        }

        $argv = func_get_args();
        if ($argc > 1) {
            $group = $argv;
        } else {
            $group = array_shift($argv);
        }

        if ($this->has('captcha')) {
            $name = $this->get('captcha')->getName();
            if (!in_array($name, $group)) {
                $group[] = $name;
            }
        }

        if ($this->has('csrf')) {
            $name = $this->get('csrf')->getName();
            if (!in_array($name, $group)) {
                $group[] = $name;
            }
        }

        if ($this->has('submit')) {
            if (!in_array('submit', $group)) {
                $group[] = 'submit';
            }
        }

        if ($this->has('reset')) {
            if (!in_array('reset', $group)) {
                $group[] = 'reset';
            }
        }

        $this->removeFieldsetElementGroup($group, $this);
        $this->setValidationGroup($group);

        return $this;
    }

    /**
     * @param array $group
     * @param FieldsetInterface $interface
     * @return void
     */
    private function removeFieldsetElementGroup(array $group, FieldsetInterface $fieldset)
    {
        $elements = $fieldset->getElements();
        foreach ($elements as $fieldsetOrElement) {
            $name = $fieldsetOrElement->getName();
            if (!in_array($name, $group)) {
                $fieldset->remove($name);
            }
        }

        $fieldsets = $fieldset->getFieldsets();
        foreach ($fieldsets as $fieldsetOrElement) {
            $name = $fieldsetOrElement->getName();
            if (!in_array($name, $group) && !array_key_exists($name, $group)) {
                $fieldset->remove($name);
            } elseif (!empty($group[$name])) {
                $this->removeFieldsetElementGroup($group[$name], $fieldsetOrElement);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setData($data)
    {
        if ($data instanceof \Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }
        if (!is_array($data)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }

        $data = self::filterFormData($this, $data);
        return parent::setData($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareBindData(array $values, array $match)
    {
        $data = [];
        foreach ($values as $name => $value) {
            if (!array_key_exists($name, $match)) {
                if (is_array($value)) {
                    if (!($value = array_filter($value))) {
                        $data[$name] = [];
                    }
                }
                continue;
            }
            if (is_array($value) && is_array($match[$name])) {
                $data[$name] = $this->prepareBindData($value, $match[$name]);
            } else {
                $data[$name] = $value;
            }
        }

        return $data;
    }

    /**
     * @static
     * @param ElementInterface $element
     * @param array|\ArrayAccess|\Traversable $data
     * @return null|array|\ArrayAccess|\Traversable
     */
    protected static function filterFormData(ElementInterface $element, $data)
    {
        if (is_array($data)) {
            if ($element instanceof Collection) {
                // Collections are to be recursed
                foreach ($data as $key => $value) {
                    $data[$key] = self::filterFormData($element->getTargetElement(), $value);
                }
            } elseif ($element instanceof FieldsetInterface) {
                // Fieldsets are to be recursed
                foreach ($data as $key => $value) {
                    if ($element->has($key)) {
                        $data[$key] = self::filterFormData($element->get($key), $value);
                    } else {
                        unset($data[$key]);
                    }
                }
            } else {
                // Array for a normal element, make sure there is ANY data in the array
                if (count(array_filter($data)) > 0) {
                    return $data;
                } else {
                    return $data; // null;
                }
            }
        } elseif ($element instanceof Collection && $element->getOption('count') == 0) {
            $data = $data ?: [];
        }

        return $data;
    }
}
