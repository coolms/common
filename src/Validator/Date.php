<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Validator;

use Zend\Validator\Date as ZendDateValidator;

class Date extends ZendDateValidator
{
    /**
     * @var bool
     */
    protected $allowNull = false;

    /**
     * @return bool
     */
    public function isAllowNull()
    {
        return $this->allowNull;
    }

    /**
     * Sets the allowNull option
     *
     * @param  bool $flag
     * @return self
     */
    public function setAllowNull($flag = true)
    {
        $this->allowNull = $flag;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid($value)
    {
        if (null === $value && $this->isAllowNull()) {
            return true;
        }

        return parent::isValid($value);
    }
}
