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

use DateTime,
    IntlDateFormatter,
    Locale,
    Zend\Filter\DateSelect as DateSelectFilter,
    Zend\Filter\StaticFilter,
    Zend\Validator\Between,
    Zend\Validator\Callback,
    Zend\Validator\GreaterThan,
    Zend\Validator\LessThan,
    CmsCommon\Form\InputFilterProviderFieldset,
    CmsCommon\Mapping\Dateable\RangeableInterface,
    CmsCommon\Stdlib\ArrayUtils;

class DateSelectRange extends InputFilterProviderFieldset
{
    /**
     * @var DateTime
     */
    protected $minStartDate;

    /**
     * @var DateTime
     */
    protected $maxStartDate;

    /**
     * @var DateTime
     */
    protected $minEndDate;

    /**
     * @var DateTime
     */
    protected $maxEndDate;

    /**
     * @var string|Locale
     */
    protected $locale;

    /**
     * @var IntlDateFormatter
     */
    protected $dateFormatter;

    /**
     * The class or interface of objects that can be bound to this fieldset.
     *
     * @var string
     */
    protected $allowedObjectBindingClass = 'CmsCommon\\Mapping\\Dateable\\RangeableInterface';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->add([
            'name' => 'startDate',
            'type' => 'DateSelect',
            'options' => [
                'label' => 'since',
                'text_domain' => $this->getOption('text_domain') ?: 'default',
            ],
        ]);

        $this->add([
            'name' => 'endDate',
            'type' => 'DateSelect',
            'options' => [
                'label' => 'to',
                'text_domain' => $this->getOption('text_domain') ?: 'default',
            ],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        foreach ($this as $element) {
            if (isset($options['day_attributes'])) {
                $element->setDayAttributes($options['day_attributes']);
            }

            if (isset($options['month_attributes'])) {
                $element->setMonthAttributes($options['month_attributes']);
            }

            if (isset($options['year_attributes'])) {
                $element->setYearAttributes($options['year_attributes']);
            }

            if (isset($options['min_year'])) {
                $element->setMinYear($options['min_year']);
            }

            if (isset($options['max_year'])) {
                $element->setMaxYear($options['max_year']);
            }

            if (isset($options['create_empty_option'])) {
                $element->setShouldCreateEmptyOption($options['create_empty_option']);
            }

            if (isset($options['render_delimiters'])) {
                $element->setShouldRenderDelimiters($options['render_delimiters']);
            }
        }

        if (isset($options['locale'])) {
            $this->setLocale($options['locale']);
        }

        if (isset($options['min_start_date'])) {
            $this->setMinStartDate($options['min_start_date']);
        }

        if (isset($options['max_start_date'])) {
            $this->setMaxStartDate($options['max_start_date']);
        }

        if (isset($options['min_end_date'])) {
            $this->setMinEndDate($options['min_end_date']);
        }

        if (isset($options['max_end_date'])) {
            $this->setMaxEndDate($options['max_end_date']);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getInputFilterSpecification()
    {
        $inputFilterSpec = parent::getInputFilterSpecification();

        $inputFilterSpec['startDate'] = [
            'name' => 'startDate',
            'allow_empty' => !$this->getOption('create_empty_option'),
            'validators' => [],
        ];

        $inputFilterSpec['endDate'] = [
            'name' => 'endDate',
            'allow_empty' => !$this->getOption('create_empty_option'),
            'validators' => [
                [
                    'name' => 'Callback',
                    'options' => [
                        'messages' => [
                            Callback::INVALID_VALUE => 'The end date '
                            . 'must be later or equal to the start date',
                        ],
                        'callback' => function($value, $context = [])
                        {
                            $filter = new DateSelectFilter(['null_on_empty' => true]);
                            if ($startDate = $filter->filter($context['startDate'])) {
                                return new DateTime($value) >= new DateTime($startDate);
                            }

                            return false;
                        },
                        'break_chain_on_failure' => true,
                    ],
                ],
            ],
        ];

        foreach ($this as $element) {
            $name = $element->getName();
            $minGetter = 'getMin' . ucfirst($name);
            $maxGetter = 'getMax' . ucfirst($name);
            if (!method_exists($this, $minGetter) || !method_exists($this, $maxGetter)) {
                continue;
            }

            if ($this->$minGetter()) {
                $inputFilterSpec[$name]['validators'][] = [
                    'name' => 'GreaterThan',
                    'options' => [
                        'messages' => [
                            GreaterThan::NOT_GREATER_INCLUSIVE => 'The date '
                                . 'must be not earlier than %min% inclusive',
                        ],
                        'messageVariables' => [
                            'min' => ['abstractOptions' => 'fmt'],
                        ],
                        'min' => $this->$minGetter()->format('Y-m-d'),
                        'fmt' => $this->format($this->$minGetter()),
                        'inclusive' => true,
                        'break_chain_on_failure' => true,
                    ],
                ];
            }
            if ($this->$maxGetter()) {
                $inputFilterSpec[$name]['validators'][] = [
                    'name' => 'LessThan',
                    'options' => [
                        'messages' => [
                            LessThan::NOT_LESS_INCLUSIVE => 'The date '
                                . 'must be not later than %max% inclusive',
                        ],
                        'messageVariables' => [
                            'max' => ['abstractOptions' => 'fmt'],
                        ],
                        'max' => $this->$maxGetter()->format('Y-m-d'),
                        'fmt' => $this->format($this->$maxGetter()),
                        'inclusive' => true,
                        'break_chain_on_failure' => true,
                    ],
                ];
            }
        }

        return $inputFilterSpec;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        if ($value instanceof RangeableInterface) {
            $this->get('startDate')->setValue($value->getStartDate());
            $this->get('endDate')->setValue($value->getEndDate());
            return $this;
        }

        if ($value instanceof \Traversable) {
            $value = ArrayUtils::iteratorToArray($value, false);
        }

        if (is_array($value)) {
            foreach ($value as $name => $val) {
                if ($this->has($name)) {
                    $this->get($name)->setValue($val);
                }
            }
        }

        return $this;
    }

    /**
     * @param string|Locale $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = Locale::canonicalize($locale);
        return $this;
    }

    /**
     * @return string|Locale
     */
    public function getLocale()
    {
        if (null === $this->locale) {
            return Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * @param string|int|DateTime $date
     * @return self
     */
    public function setMinStartDate($date)
    {
        $this->minStartDate = $this->normalizeDateTime($date);
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMinStartDate()
    {
        if (null === $this->minStartDate && ($object = $this->getObject())) {
            return $object->getStartDate();
        }

        return $this->minStartDate;
    }

    /**
     * @param string|int|DateTime $date
     * @return self
     */
    public function setMaxStartDate($date)
    {
        $this->maxStartDate = $this->normalizeDateTime($date);
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMaxStartDate()
    {
        if (null === $this->maxStartDate && ($object = $this->getObject())) {
            return $object->getEndDate();
        }

        return $this->maxStartDate;
    }

    /**
     * @param string|int|DateTime $date
     * @return self
     */
    public function setMinEndDate($date)
    {
        $this->minEndDate = $this->normalizeDateTime($date);
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMinEndDate()
    {
        if (null === $this->minEndDate && ($object = $this->getObject())) {
            return $object->getStartDate();
        }

        return $this->minEndDate;
    }

    /**
     * @param string|int|DateTime $date
     * @return self
     */
    public function setMaxEndDate($date)
    {
        $this->maxEndDate = $this->normalizeDateTime($date);
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMaxEndDate()
    {
        if (null === $this->maxEndDate && ($object = $this->getObject())) {
            return $object->getEndDate();
        }

        return $this->maxEndDate;
    }

    /**
     * @param DateTime $date
     * @return string
     */
    protected function format(DateTime $date)
    {
        return $this->getDateFormatter()->format($date);
    }

    /**
     * Normalize the provided value to a DateTime object
     *
     * @param  string|int|DateTime $value
     * @return DateTime
     */
    protected function normalizeDateTime($value)
    {
        try {
            if (is_int($value)) {
                //timestamp
                $value = new DateTime("@$value");
            } elseif (!$value instanceof DateTime) {
                $value = new DateTime($value);
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid date string provided', $e->getCode(), $e);
        }

        return $value;
    }

    /**
     * @return IntlDateFormatter
     */
    protected function getDateFormatter()
    {
        if (null === $this->dateFormatter) {
            $this->dateFormatter = new IntlDateFormatter(
                $this->getLocale(),
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE
            );
        }

        return $this->dateFormatter;
    }
}
