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

use Zend\Form\Element\MonthSelect as ZendMonthSelect,
    Zend\Validator\Date as DateValidator;

class MonthSelect extends ZendMonthSelect
{
    use MonthSelectYearTrait;

    /**
     * {@inheritDoc}
     */
    protected function getValidator()
    {
        if (null === $this->validator) {
            $this->validator = new DateValidator(['format' => 'Y-m', 'allowNull' => true]);
        }

        return $this->validator;
    }

    /**
     * {@inheritDoc}
     */
    public function getInputSpecification()
    {
        $inputSpec = parent::getInputSpecification();

        $inputSpec['required'] = false;
        $inputSpec['filters'] = [
            ['name' => 'MonthSelect', 'options' => ['null_on_empty' => true]],
        ];

        return $inputSpec;
    }
}
