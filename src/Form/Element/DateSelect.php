<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\Element;

use Zend\Form\Element\DateSelect as DateSelectElement,
    Zend\Validator,
    CmsCommon\Stdlib\ArrayUtils;

class DateSelect extends DateSelectElement
{
    use MonthSelectTrait;

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        parent::setOptions($options);
    
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($options['max_date'])) {
            $this->setMaxDate($options['max_date']);
        }

        if (isset($options['min_date'])) {
            $this->setMinDate($options['min_date']);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = [
                'name' => 'Date',
                'options' => [
                    'format' => 'Y-m-d',
                    'allowNull' => !!$this->shouldCreateEmptyOption(),
                ],
            ];
        }

        return $this->validator;
    }

    /**
     * {@inheritDoc}
     */
    public function getInputSpecification()
    {
        $inputSpec = parent::getInputSpecification();

        $inputSpec['filters'][] = [
            'name' => 'DateSelect',
            'options' => [
                'null_on_empty' => !!$this->shouldCreateEmptyOption(),
            ],
        ];

        if ($date = $this->getMinDate()) {
            $inputSpec['validators'][] = [
                'name' => 'GreaterThan',
                'options' => [
                    'messages' => [
                        Validator\GreaterThan::NOT_GREATER_INCLUSIVE => 'The date '
                            . 'must be not earlier than %min% inclusive',
                    ],
                    'messageVariables' => [
                        'min' => ['abstractOptions' => 'fmt'],
                    ],
                    'min' => $date->format('Y-m-d'),
                    'fmt' => $this->format($date),
                    'inclusive' => true,
                    'break_chain_on_failure' => true,
                ],
            ];
        }

        if ($date = $this->getMaxDate()) {
            $inputSpec['validators'][] = [
                'name' => 'LessThan',
                'options' => [
                    'messages' => [
                        Validator\LessThan::NOT_LESS_INCLUSIVE => 'The date '
                            . 'must be not later than %max% inclusive',
                    ],
                    'messageVariables' => [
                        'max' => ['abstractOptions' => 'fmt'],
                    ],
                    'max' => $date->format('Y-m-d'),
                    'fmt' => $this->format($date),
                    'inclusive' => true,
                    'break_chain_on_failure' => true,
                ],
            ];
        }

        return $inputSpec;
    }
}
