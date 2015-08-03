<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mvc\Controller\Options;

use Zend\Stdlib\AbstractOptions;

class CrudControllerOptions extends AbstractOptions implements CrudControllerOptionsInterface
{
    use Traits\ControllerOptionsTrait,
        Traits\CrudControllerOptionsTrait;

    /**
     * @var bool
     */
    protected $__strictMode__ = false;
}
