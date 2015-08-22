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

use Zend\View\Helper\AbstractHelper,
    Zend\Json\Expr;

/**
 * View helper for rendering.
 */
class JsonExpr extends AbstractHelper
{
    /**
     * Returns new Expr object
	 *
	 * @param string $expression
	 * @return Expr
     */
    public function __invoke($expression)
    {
        return new Expr($expression);
    }
}
