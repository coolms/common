<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper\Type;

use InvalidArgumentException,
    Zend\View\Helper\AbstractHelper as AbstractViewHelper;

abstract class AbstractHelper extends AbstractViewHelper
{
    /**
     * @var string
     */
    protected $instanceType;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $value
     * @return self|string
     */
    public function __invoke($value = null)
    {
        if (null === $value) {
            return $this;
        }

        return $this->render($value);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function render($value)
    {
        $this->guardValue($value);
        return $this->format($value);
    }

    /**
     * @param mixed $value
     * @throws InvalidArgumentException
     * @return bool
     */
    protected function guardValue($value)
    {
        if (null !== $this->instanceType) {
            if (!$value instanceof $this->instanceType) {
                throw new InvalidArgumentException(sprintf(
                    'Formatted value must be an instance of %s; %s given',
                    $this->instanceType,
                    is_object($value) ? get_class($value) : gettype($value)
                ));
            }
        }

        if (null !== $this->type && function_exists("is_{$this->type}")) {
            if (!call_user_func("is_{$this->type}", $value)) {
                throw new InvalidArgumentException(sprintf(
                    'Formatted value must be a type of %s; %s given',
                    $this->type,
                    is_object($value) ? get_class($value) : gettype($value)
                ));
            }
        }

        return true;
    }

    /**
     * @param mixed $value
     * @return string
     */
    abstract protected function format($value);
}
