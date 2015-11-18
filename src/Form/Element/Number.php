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

use Zend\Form\Element\Number as BaseNumber;

class Number extends BaseNumber
{
    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($options['max'])) {
            $this->setMax($options['max']);
        }

        if (isset($options['min'])) {
            $this->setMin($options['min']);
        }

        if (isset($options['step'])) {
            $this->setStep($options['step']);
        }
    }

    /**
     * @param number $max
     * @return self
     */
    public function setMax($max)
    {
        $this->setAttribute('max', (float) $max);
        return $this;
    }

    /**
     * @return number
     */
    public function getMax()
    {
        return $this->getAttribute('max');
    }

    /**
     * @param number $min
     * @return self
     */
    public function setMin($min)
    {
        $this->setAttribute('min', (float) $min);
        return $this;
    }

    /**
     * @return number
     */
    public function getMin()
    {
        return $this->getAttribute('min');
    }

    /**
     * @param number $step
     * @return self
     */
    public function setStep($step)
    {
        $this->setAttribute('step', (float) $step);
        return $this;
    }

    /**
     * @return number
     */
    public function getStep()
    {
        return $this->getAttribute('step');
    }

    /**
     * {@inheritDoc}
     */
    protected function getValidators()
    {
        if (strlen($this->getValue()) === 0) {
            return [];
        }

        return parent::getValidators();
    }

    /**
     * {@inheritDoc}
     */
    public function getInputSpecification()
    {
        $inputSpec = parent::getInputSpecification();
        $inputSpec['allow_empty'] = true;

        return $inputSpec;
    }
}
