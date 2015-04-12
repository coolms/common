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

interface ContainerProviderInterface
{
    /**
     * @return AbstractContainer
     */
    public function getSessionContainer();

    /**
     * @param AbstractContainer $conatiner
     * @return self
     */
    public function setSessionContainer(AbstractContainer $container);
}
