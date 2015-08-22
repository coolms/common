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

use Zend\Http\Request as HttpRequest,
    Zend\Mvc\MvcEvent,
    Zend\Stdlib\RequestInterface,
    Zend\View\Helper\AbstractHelper,
    CmsCommon\View\Exception\RuntimeException;

class Cookie extends AbstractHelper
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * __construct
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Grabs a param from cookie.
     *
     * @param string $param
     * @param mixed $default
     * @return mixed
     */
    public function __invoke($param = null, $default = null)
    {
        if ($this->request instanceof HttpRequest) {
            $cookie = $this->request->getCookie();
            if ($param === null) {
                return $cookie;
            }

            if ($cookie->offsetExists($param)) {
                return $cookie->get($param);
            }

            return $default;
        }
    }
}
