<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mapping\Hierarchy;

/**
 * Interface for the model that is part of the hierarchy with multiple roots
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface RootableInterface
{
    /**
     * @return string
     */
    public function getRoot();

    /**
     * @param string $root
     * @return self
     */
    public function setRoot($root);
}
