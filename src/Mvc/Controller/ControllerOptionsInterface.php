<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mvc\Controller;

interface ControllerOptionsInterface
{
    /**
     * Sets controller FQCN
     *
     * @param string $type
     * @return self
     */
    public function setControllerType($type);

    /**
     * Retrieves controller FQCN
     *
     * @return string
     */
    public function getControllerType();

    /**
     * Sets request identifier param name
     *
     * @param string $key
     * @return self
     */
    public function setIdentifierKey($key);

    /**
     * Retrieves request identifier param name
     *
     * @return string
     */
    public function getIdentifierKey();

    /**
     * Sets base route
     *
     * @param string $route
     * @return self
     */
    public function setBaseRoute($route);

    /**
     * Retrieves base route
     *
     * @return string
     */
    public function getBaseRoute();
}
