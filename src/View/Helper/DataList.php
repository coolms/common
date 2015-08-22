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
    CmsCommon\Persistence\MapperInterface;

/**
 * View Helper to render lists of data
 */
class DataList extends AbstractHelper
{
    /**
     * @param MapperInterface   $mapper
     * @param HttpRequest       $request
     * @return self|string
     */
    public function __invoke(MapperInterface $mapper = null)
    {
        if (0 === func_num_args()) {
            return $this;
        }

        return $this->render($mapper);
    }

    /**
     * Renders list
     *
     * @param MapperInterface $mapper
     * @return string
     */
    public function render(MapperInterface $mapper)
    {
        
    }
}
