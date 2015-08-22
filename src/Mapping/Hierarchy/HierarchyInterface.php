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
 * Interface for the model that is part of the hierarchy
 * 
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface HierarchyInterface
{
    /**
     * @param int $level
     */
    public function setLevel($level);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @return HierarchyInterface
     */
    public function getParent();

    /**
     * @param HierarchyInterface $parent
     */
    public function setParent(HierarchyInterface $parent = null);

    /**
     * @return HierarchyInterface[]
     */
    public function getChildren();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @param HierarchyInterface[] $children
     */
    public function setChildren($children);

    /**
     * @param HierarchyInterface[] $children
     */
    public function addChildren($children);

    /**
     * @param HierarchyInterface $child
     */
    public function addChild(HierarchyInterface $child);

    /**
     * @param HierarchyInterface[] $children
     */
    public function removeChildren($children);

    /**
     * @param HierarchyInterface $child
     */
    public function removeChild(HierarchyInterface $child);

    /**
     * @param HierarchyInterface $child
     * @return bool
     */
    public function hasChild(HierarchyInterface $child);

    /**
     * Removes all children
     */
    public function clearChildren();
}
