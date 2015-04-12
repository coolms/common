<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mapping\Hierarchy\Traits;

use CmsCommon\Mapping\Hierarchy\HierarchyInterface;

/**
 * Trait for the model to be a part of the hierarchy
 * 
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait HierarchyTrait
{
    /**
     * @var HierarchyInterface
     */
    protected $parent;

    /**
     * @var HierarchyInterface[]
     */
    protected $children;

    /**
     * @param HierarchyInterface $parent
     */
    public function setParent(HierarchyInterface $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return HierarchyInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param HierarchyInterface[] $children
     */
    public function setChildren($children)
    {
        $this->clearChildren();
        $this->addChildren($children);
    }

    /**
     * @param HierarchyInterface[] $children
     */
    public function addChildren($children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param HierarchyInterface $child
     */
    public function addChild(HierarchyInterface $child)
    {
        $this->children[] = child;
    }

    /**
     * @param HierarchyInterface[] $children
     */
    public function removeChildren($children)
    {
        foreach ($children as $child) {
            $this->removeChild($child);
        }
    }

    /**
     * @abstract
     * @param HierarchyInterface $child
     */
    abstract public function removeChild(HierarchyInterface $child);

    /**
     * Removes all children
     */
    public function clearChildren()
    {
        $this->children = [];
    }

    /**
     * @abstract
     * @param HierarchyInterface $child
     * @return bool
     */
    abstract public function hasChild(HierarchyInterface $child);

    /**
     * @return HierarchyInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }
}
