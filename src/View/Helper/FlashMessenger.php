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

use Zend\Mvc\Controller\Plugin\FlashMessenger as PluginFlashMessenger,
    Zend\View\Helper\FlashMessenger as ZendFlashMessenger;

/**
 * Helper to proxy the plugin flash messenger
 */
class FlashMessenger extends ZendFlashMessenger
{
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
