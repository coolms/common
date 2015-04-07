<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper;

use Zend\Form\ElementInterface,
    Zend\Form\View\Helper\FormElementErrors,
    Zend\Mvc\Controller\Plugin\FlashMessenger;

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
        FlashMessenger::NAMESPACE_INFO    => 'info',
        FlashMessenger::NAMESPACE_ERROR   => 'error',
        FlashMessenger::NAMESPACE_SUCCESS => 'success',
        FlashMessenger::NAMESPACE_DEFAULT => 'default',
        FlashMessenger::NAMESPACE_WARNING => 'warning',
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
                $attribs  = $attributes;
                if ($namespace === FlashMessenger::NAMESPACE_ERROR) {

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
                        $element->setMessages(array_merge(
                            $element->getMessages(),
                            $this->translateMessages($messages)
                        ));
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
     * @param array $messages
     * @return array
     */
    protected function translateMessages(array $messages)
    {
        $messagesToPrint = [];

        $escapeHtml = $this->getEscapeHtmlHelper();
        $translator = $this->getTranslator();
        $textDomain = $this->getTranslatorTextDomain();

        array_walk_recursive($messages, function ($item) use (&$messagesToPrint, $escapeHtml, $translator, $textDomain) {
            if ($translator !== null) {
                $item = $translator->translate($item, $textDomain);
            }
            $messagesToPrint[] = $escapeHtml($item);
        });

        return $messagesToPrint;
    }
}
