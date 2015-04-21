<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper;

use Zend\Mvc\Controller\Plugin\FlashMessenger as FlashMessengerPlugin;

class Messages extends HtmlContainer
{
    /**
     * @var string
     */
    protected $defaultClass = 'cms-messages';

    /**
     * @var FlashMessenger
     */
    protected $flashMessengerHelper;

    /**
     * {@inheritDoc}
     */
    public function render($content, array $attribs = [])
    {
        if (!$content) {
            /* @var $fm FlashMessenger */
            $fm = $this->getFlashMessengerHelper();

            if ($fm->hasMessages()) {
                $content .= $fm->render();
            }
            if ($fm->hasInfoMessages()) {
                $content .= $fm->render(FlashMessengerPlugin::NAMESPACE_INFO);
            }
            if ($fm->hasSuccessMessages()) {
                $content .= $fm->render(FlashMessengerPlugin::NAMESPACE_SUCCESS);
            }
            if ($fm->hasWarningMessages()) {
                $content .= $fm->render(FlashMessengerPlugin::NAMESPACE_WARNING);
            }
            if ($fm->hasErrorMessages()) {
                $content .= $fm->render(FlashMessengerPlugin::NAMESPACE_ERROR);
            }
        }

        return parent::render($content, $attribs);
    }

    /**
     * Retrieve the flashMessenger helper
     *
     * @return FlashMessenger
     */
    protected function getFlashMessengerHelper()
    {
        if ($this->flashMessengerHelper) {
            return $this->flashMessengerHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->flashMessengerHelper = $this->view->plugin('flashmessenger');
        }

        if (!$this->flashMessengerHelper instanceof FlashMessenger) {
            $this->flashMessengerHelper = new FlashMessenger();
            $this->flashMessengerHelper->setView($this->getView());
        }

        return $this->flashMessengerHelper;
    }
}
