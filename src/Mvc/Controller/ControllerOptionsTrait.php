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

trait ControllerOptionsTrait
{
    /**
     * @var string controller FQCN
     */
    protected $controllerType;

    /**
     * @var string
     */
    protected $identifierKey = 'id';

    /**
     * @var string
     */
    protected $baseRoute;

    /**
     * Sets controller FQCN
     *
     * @param string $type
     * @return self
     */
    public function setControllerType($type)
    {
        $this->controllerType = $type;

        return $this;
    }

    /**
     * Retrieves controller FQCN
     *
     * @return string
     */
    public function getControllerType()
    {
        return $this->controllerType;
    }

    /**
     * Sets request identifier param name
     *
     * @param string $key
     * @return self
     */
    public function setIdentifierKey($key)
    {
        $this->identifierKey = $key;

        return $this;
    }

    /**
     * Retrieves request identifier param name
     *
     * @return string
     */
    public function getIdentifierKey()
    {
        return $this->identifierKey;
    }

    /**
     * Sets base route
     *
     * @param string $route
     * @return self
     */
    public function setBaseRoute($route)
    {
        $this->baseRoute = $route;

        return $this;
    }

    /**
     * Retrieves base route
     *
     * @return string
     */
    public function getBaseRoute()
    {
        return $this->baseRoute;
    }
}
