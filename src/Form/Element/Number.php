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

        $inputSpec['required'] = false;

        return $inputSpec;
    }
}
