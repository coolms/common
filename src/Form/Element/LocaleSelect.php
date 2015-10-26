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

use Locale,
    Zend\Form\Element\Select as ZendSelect,
    CmsCommon\Stdlib\LocaleUtils;

class LocaleSelect extends ZendSelect
{
    /**
     * List of allowed locales
     *
     * @var array
     */
    protected $localeList;

    /**
     * Default attributes
     *
     * @var array
     */
    protected $attrubutes = [
        'required' => true,
    ];

    /**
     * Default options
     *
     * @var array
    */
    protected $options = [
        'display_names' => true,
    ];

    /**
     * __construct
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, $options = [])
    {
        if (null === $name) {
            $name = 'locale-select';
        }

        parent::__construct($name, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function getValueOptions()
    {
        if (!count($this->valueOptions)) {
            $locales = $this->getLocaleList();
            $options = parent::getValueOptions();
            foreach ($locales as $code => $name) {
                $options[$code] = $this->getDisplayNames() ? $name : $code;
            }

            $this->setValueOptions($options);
        }

        return parent::getValueOptions();
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        if ($this->isMultiple()) {
            if ($value instanceof \Traversable) {
                $value = ArrayUtils::iteratorToArray($value);
            } elseif (!$value) {
                return parent::setValue([]);
            } elseif (!is_array($value)) {
                $value = (array) $value;
            }

            return parent::setValue(array_map(Locale::class . '::canonicalize', $value));
        }

        return parent::setValue(Locale::canonicalize($value));
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        if (null === ($value = parent::getValue())) {
            return (string) Locale::getDefault();
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = [
                //'name' => 'LocaleCode',
            ];
        }

        return $this->validator;
    }

    /**
     * Set validator to return with input spec
     *
     * @param  array|\Zend\Validator\ValidatorInterface $validator
     * @return self
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getInputSpecification()
    {
        $inputSpec = parent::getInputSpecification();

        $defaultFilters = [
            ['name' => 'StringTrim'],
        ];

        if (isset($inputSpec['filters'])) {
            array_unshift($inputSpec['filters'], $defaultFilters);
        } else {
            $inputSpec['filters'] = $defaultFilters;
        }

        return $inputSpec;
    }

    /**
     * Set Option whether to display names or codes
     *
     * @param  bool $flag
     * @return self
     */
    public function setDisplayNames($flag)
    {
        $this->setOption('display_names', (bool) $flag);
        return $this;
    }

    /**
     * Return display names option
     *
     * @return bool
     */
    public function getDisplayNames()
    {
        return $this->getOption('display_names');
    }

    /**
     * Set locale list to check allowed locales against
     *
     * @param  array $list
     * @return self
     */
    public function setLocaleList(array $list)
    {
        $this->localeList = $list;
        return $this;
    }

    /**
     * Return the locale list for checking allowed locales
     *
     * Lazy loads one if none set
     *
     * @return array
     */
    public function getLocaleList()
    {
        if (null === $this->localeList) {
            $this->setLocaleList(LocaleUtils::getNamedList(Locale::getDefault()));
        }

        return $this->localeList;
    }
}
