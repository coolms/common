<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper;

use Zend\Form\ElementInterface,
    Zend\Form\View\Helper\FormElementErrors,
    Zend\Mvc\Controller\Plugin\FlashMessenger as PluginFlashMessenger;

class FormMessages extends FormElementErrors
{
    /**@+
     * @var string Templates for the open/close for message tags
     */
    protected $messageCloseString;
    protected $messageSeparatorString;
    protected $messageOpenFormat;
    /**@-*/

    /**
     * Default attributes for the open format tag
     *
     * @var array
     */
    protected $classMessages = [
        PluginFlashMessenger::NAMESPACE_INFO    => 'info',
        PluginFlashMessenger::NAMESPACE_ERROR   => 'error',
        PluginFlashMessenger::NAMESPACE_SUCCESS => 'success',
        PluginFlashMessenger::NAMESPACE_DEFAULT => 'default',
        PluginFlashMessenger::NAMESPACE_WARNING => 'warning',
    ];

    /**
     * {@inheritDoc}
     */
    public function render(ElementInterface $element, array $attributes = [])
    {
        $markup = '';

        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            return $markup;
        }

        /* @var $flashMessenger \Zend\View\Helper\FlashMessenger */
        $flashMessenger = $renderer->plugin('flashmessenger');
        if ($flashMessenger->hasCurrentMessages() || $element->getMessages())
        {
            $formName = $element->getName();
            foreach ($this->classMessages as $namespace => $class)
            {
                $attribs = $attributes;
                if ($namespace === PluginFlashMessenger::NAMESPACE_ERROR) {

                    $flashMessenger->setTranslatorTextDomain($this->getTranslatorTextDomain());

                    if (!$this->getMessageOpenFormat()) {
                        $this->setMessageOpenFormat($flashMessenger->getMessageOpenFormat());
                    }
                    if (!$this->getMessageSeparatorString()) {
                        $this->setMessageSeparatorString($flashMessenger->getMessageSeparatorString());
                    }
                    if (!$this->getMessageCloseString()) {
                        $this->setMessageCloseString($flashMessenger->getMessageCloseString());
                    }

                    if ($flashMessenger->hasCurrentMessages() &&
                        ($messages = $flashMessenger->getCurrentMessagesFromNamespace("$formName-$namespace"))
                    ) {
                        $element->setMessages(array_merge($element->getMessages(), $messages));
                    }

                    if (isset($attribs['class'])) {
                        $attribs['class'] .= ' ' . $class;
                    } else {
                        $attribs['class'] = $class;
                    }

                    $markup .= parent::render($element, $attribs);

                } elseif ($flashMessenger->hasCurrentMessages()) {
                    if (isset($attribs['class'])) {
                        $class = array_merge((array) $attribs['class'], (array) $class);
                    }

                    $markup .= $flashMessenger->renderCurrent("$formName-$namespace", (array) $class);
                }
            }
        }

        return $markup;
    }

    /**
     * Set messages classes
     *
     * @param  array $classMessages
     * @return self
     */
    public function setClassMessages(array $classMessages)
    {
        $this->classMessages = array_replace($this->classMessages, $classMessages);
        return $this;
    }

    /**
     * Get messages classes
     *
     * @return array
     */
    public function getClassMessages()
    {
        return $this->classMessages;
    }
}
