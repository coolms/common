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

use Zend\Form\Element\Collection as ZendCollection,
    Zend\Form\FieldsetInterface,
    Zend\Form\Exception;

class Collection extends ZendCollection
{
    /**
     * {@inheritDoc}
     */
    public function populateValues($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of data; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }

        // Can't do anything with empty data
        if (empty($data) && $this->getOption('count') != 0) {
            return;
        }

        if (!$this->allowRemove && count($data) < $this->count) {
            throw new Exception\DomainException(sprintf(
                'There are fewer elements than specified in the collection (%s). Either set the allow_remove option '
                . 'to true, or re-submit the form.',
                get_class($this)
            ));
        }

        // Check to see if elements have been replaced or removed
        foreach ($this->iterator as $name => $elementOrFieldset) {
            if (isset($data[$name])) {
                continue;
            }

            if (!$this->allowRemove) {
                throw new Exception\DomainException(sprintf(
                    'Elements have been removed from the collection (%s) but the allow_remove option is not true.',
                    get_class($this)
                ));
            }

            $this->remove($name);
        }

        foreach ($data as $key => $value) {
            if ($this->has($key)) {
                $elementOrFieldset = $this->get($key);
            } else {
                $elementOrFieldset = $this->addNewTargetElementInstance($key);
        
                if ($key > $this->lastChildIndex) {
                    $this->lastChildIndex = $key;
                }
            }

            if ($elementOrFieldset instanceof FieldsetInterface) {
                $elementOrFieldset->populateValues($value);
            } else {
                $elementOrFieldset->setAttribute('value', $value);
            }
        }

        if (!$this->createNewObjects()) {
            $this->replaceTemplateObjects();
        }
    }
}
