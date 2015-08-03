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

use Traversable,
    Zend\Filter\Exception,
    Zend\Filter\FilterChain,
    Zend\InputFilter\CollectionInputFilter,
    Zend\InputFilter\Factory as InputFilterFactory,
    Zend\InputFilter\InputFilterInterface,
    Zend\InputFilter\InputFilterProviderInterface,
    Zend\InputFilter\InputInterface,
    Zend\Stdlib\ArrayUtils,
    Zend\Validator\ValidatorChain;

class Factory extends InputFilterFactory
{
    /**
     * Factory for input filters
     *
     * @param  array|Traversable|InputFilterProviderInterface $inputFilterSpecification
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     * @return InputFilterInterface
     */
    public function createInputFilter($inputFilterSpecification)
    {
        if ($inputFilterSpecification instanceof InputFilterProviderInterface) {
            $inputFilterSpecification = $inputFilterSpecification->getInputFilterSpecification();
        }

        if (!is_array($inputFilterSpecification) && !$inputFilterSpecification instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable; received "%s"',
                __METHOD__,
                (is_object($inputFilterSpecification)
                    ? get_class($inputFilterSpecification)
                    : gettype($inputFilterSpecification))
            ));
        }
        if ($inputFilterSpecification instanceof Traversable) {
            $inputFilterSpecification = ArrayUtils::iteratorToArray($inputFilterSpecification);
        }

        $type = 'CmsCommon\InputFilter\InputFilter';

        if (isset($inputFilterSpecification['type']) && is_string($inputFilterSpecification['type'])) {
            $type = $inputFilterSpecification['type'];
            unset($inputFilterSpecification['type']);
        }

        $inputFilter = $this->getInputFilterManager()->get($type);

        if ($inputFilter instanceof CollectionInputFilter) {
            $inputFilter->setFactory($this);
            if (isset($inputFilterSpecification['input_filter'])) {
                $inputFilter->setInputFilter($inputFilterSpecification['input_filter']);
            }
            if (isset($inputFilterSpecification['count'])) {
                $inputFilter->setCount($inputFilterSpecification['count']);
            }
            if (isset($inputFilterSpecification['required'])) {
                $inputFilter->setIsRequired($inputFilterSpecification['required']);
            }
            return $inputFilter;
        }

        foreach ($inputFilterSpecification as $key => $value) {
            if (null === $value) {
                continue;
            }

            if (($value instanceof InputInterface)
                || ($value instanceof InputFilterInterface)
            ) {
                $input = $value;
            } else {
                switch ((string) $key) {
                    case 'validators':
                        if ($value instanceof ValidatorChain) {
                            $inputFilter->setValidatorChain($value);
                            continue 2;
                            break;
                        }
                        if (!is_array($value) && !$value instanceof Traversable) {
                            throw new Exception\RuntimeException(sprintf(
                                '%s expects the value associated with "validators" '
                                    . 'to be an array/Traversable of validators or validator specifications, '
                                    . 'or a ValidatorChain; received "%s"',
                                __METHOD__,
                                (is_object($value) ? get_class($value) : gettype($value))
                            ));
                        }

                        $this->populateValidators($inputFilter->getValidatorChain(), $value);
                        continue 2;
                        break;
                    case 'filters':
                        if ($value instanceof FilterChain) {
                            $inputFilter->setFilterChain($value);
                            continue 2;
                            break;
                        }

                        if (!is_array($value) && !$value instanceof Traversable) {
                            throw new Exception\RuntimeException(sprintf(
                                '%s expects the value associated with "filters" '
                                    . 'to be an array/Traversable of filters or filter specifications, '
                                    . 'or a FilterChain; received "%s"',
                                __METHOD__,
                                (is_object($value) ? get_class($value) : gettype($value))
                            ));
                        }

                        $this->populateFilters($inputFilter->getFilterChain(), $value);
                        continue 2;
                        break;
                    default:
                        $input = $this->createInput($value);
                }
            }

            $inputFilter->add($input, $key);
        }

        return $inputFilter;
    }
}
