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

use Traversable,
    Zend\Form\Element,
    Zend\Form\ElementInterface,
    Zend\Form\Exception,
    Zend\Form\FieldsetInterface;

/**
 * @see     https://github.com/zendframework/zf2/issues/5265
 * @author  Dmitry Popov <d.popov@altgraphic.com>
 */
trait MessagesTrait
{
    /**
     * Set a hash of element names/messages to use when validation fails
     *
     * @param  array|Traversable $messages
     * @return Element|ElementInterface|FieldsetInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setMessages($messages)
    {
        if (!is_array($messages) && !$messages instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or %s object of messages; received "%s"',
                __METHOD__,
                Traversable::class,
                (is_object($messages) ? get_class($messages) : gettype($messages))
            ));
        }

        foreach ($messages as $key => $messageSet) {
            if (!$this->has($key)) {
                $this->messages[$key] = $messageSet;
                continue;
            }

            $element = $this->get($key);
            $element->setMessages($messageSet);
        }

        return $this;
    }

    /**
     * Get validation error messages, if any
     *
     * Returns a hash of element names/messages for all elements failing
     * validation, or, if $elementName is provided, messages for that element
     * only.
     *
     * @param  null|string $elementName
     * @return array|Traversable
     * @throws Exception\InvalidArgumentException
     */
    public function getMessages($elementName = null)
    {
        if (null === $elementName) {
            $messages = $this->messages;
            foreach ($this->iterator as $name => $element) {
                $messageSet = $element->getMessages();
                if (!is_array($messageSet) &&
                    !$messageSet instanceof Traversable ||
                    empty($messageSet)
                ) {
                    continue;
                }

                $messages[$name] = $messageSet;
            }

            return $messages;
        }

        if (!$this->has($elementName)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid element name "%s" provided to %s',
                $elementName,
                __METHOD__
            ));
        }

        $element = $this->get($elementName);
        return $element->getMessages();
    }
}
