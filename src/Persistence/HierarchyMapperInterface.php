<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Persistence;

interface HierarchyMapperInterface extends MapperInterface
{
    /**
     * @param object $node
     * @param array|bool $criteria
     * @param array $options
     * @param bool $includeNode
     * @return array
     */
    public function childrenHierarchy(
        $node = null,
        $criteria = false,
        array $options = [],
        $includeNode = false
    );
}
