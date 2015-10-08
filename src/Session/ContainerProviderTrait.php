<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Session;

use Zend\Session\AbstractContainer;

trait ContainerProviderTrait
{
    /**
     * @var AbstractContainer
     */
    protected $sessionContainer;

    /**
     * @return AbstractContainer
     */
    public function getSessionContainer()
    {
        return $this->sessionContainer;
    }

    /**
     * @param AbstractContainer $container
     * @return self
     */
    public function setSessionContainer(AbstractContainer $container)
    {
        $this->sessionContainer = $container;
        return $this;
    }
}
