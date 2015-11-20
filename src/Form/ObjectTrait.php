<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form;

use Zend\Form\Exception,
    Zend\Form\FieldsetInterface,
    Zend\Stdlib\Hydrator\HydratorInterface;

trait ObjectTrait
{
    /**
     * Set the object used by the hydrator
     *
     * @param  object $object
     * @return Fieldset|FieldsetInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setObject($object)
    {
        if (!is_object($object)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an object argument; received "%s"',
                __METHOD__,
                $object
            ));
        }

        if (property_exists($this, 'hydrator') && $this->hydrator) {
            $values = $this->getHydrator()->extract($object);
            foreach ($this->getFieldsets() as $name => $fieldset) {
                if (isset($values[$name]) && is_object($values[$name])) {
                    $fieldset->setObject($values[$name]);
                }
            }
        }

        if (property_exists($this, 'object')) {
            $this->object = $object;
        }

        return $this;
    }

    /**
     * @return FieldsetInterface[]
     */
    abstract public function getFieldsets();

    /**
     * @return HydratorInterface
     */
    abstract public function getHydrator();
}
