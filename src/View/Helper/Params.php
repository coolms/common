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

use Zend\Mvc\MvcEvent,
    Zend\Stdlib\RequestInterface,
    Zend\View\Helper\AbstractHelper,
    CmsCommon\View\Exception\RuntimeException;

class Params extends AbstractHelper
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * __construct
     *
     * @param RequestInterface $request
     * @param MvcEvent $event
     */
    public function __construct(RequestInterface $request, MvcEvent $event)
    {
        $this->request = $request;
        $this->event = $event;
    }

    /**
     * Grabs a param from route match by default.
     *
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    public function __invoke($param = null, $default = null)
    {
        if ($param === null) {
            return $this;
        }
        return $this->fromRoute($param, $default);
    }

    /**
     * Return all files or a single file.
     *
     * @param  string $name File name to retrieve, or null to get all.
     * @param  mixed $default Default value to use when the file is missing.
     * @return array|\ArrayAccess|null
     */
    public function fromFiles($name = null, $default = null)
    {
        if ($name === null) {
            return $this->request->getFiles($name, $default)->toArray();
        }

        return $this->request->getFiles($name, $default);
    }

    /**
     * Return all header parameters or a single header parameter.
     *
     * @param  string $header Header name to retrieve, or null to get all.
     * @param  mixed $default Default value to use when the requested header is missing.
     * @return null|\Zend\Http\Header\HeaderInterface
     */
    public function fromHeader($header = null, $default = null)
    {
        if ($header === null) {
            return $this->request->getHeaders($header, $default)->toArray();
        }

        return $this->request->getHeaders($header, $default);
    }

    /**
     * Return all post parameters or a single post parameter.
     *
     * @param string $param Parameter name to retrieve, or null to get all.
     * @param mixed $default Default value to use when the parameter is missing.
     * @return mixed
     */
    public function fromPost($param = null, $default = null)
    {
        if ($param === null) {
            return $this->request->getPost($param, $default)->toArray();
        }

        return $this->request->getPost($param, $default);
    }

    /**
     * Return all query parameters or a single query parameter.
     *
     * @param string $param Parameter name to retrieve, or null to get all.
     * @param mixed $default Default value to use when the parameter is missing.
     * @return mixed
     */
    public function fromQuery($param = null, $default = null)
    {
        if ($param === null) {
            return $this->request->getQuery($param, $default)->toArray();
        }

        return $this->request->getQuery($param, $default);
    }

    /**
     * Return all route parameters or a single route parameter.
     *
     * @param string $param Parameter name to retrieve, or null to get all.
     * @param mixed $default Default value to use when the parameter is missing.
     * @return mixed
     * @throws RuntimeException
     */
    public function fromRoute($param = null, $default = null)
    {
        if ($param === null) {
            return $this->event->getRouteMatch()->getParams();
        }

        return $this->event->getRouteMatch()->getParam($param, $default);
    }
}
