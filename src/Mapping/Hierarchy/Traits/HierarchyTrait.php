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
    protected $children = [];

    /**
     * @param HierarchyInterface $parent
     * @return self
     */
    public function setParent(HierarchyInterface $parent = null)
    {
        $this->parent = $parent;
        return $this;
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
     * @return self
     */
    public function setChildren($children)
    {
        $this->clearChildren();
        $this->addChildren($children);

        return $this;
    }

    /**
     * @param HierarchyInterface[] $children
     * @return self
     */
    public function addChildren($children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * @param HierarchyInterface $child
     * @return self
     */
    public function addChild(HierarchyInterface $child)
    {
        $this->children[] = child;
        return $this;
    }

    /**
     * @param HierarchyInterface[] $children
     * @return self
     */
    public function removeChildren($children)
    {
        foreach ($children as $child) {
            $this->removeChild($child);
        }

        return $this;
    }

    /**
     * @param HierarchyInterface $child
     * @return self
     */
    public function removeChild(HierarchyInterface $child)
    {
        foreach ($this->children as $key => $entity) {
            if ($child === $entity) {
                unset($this->children[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Removes all children
     *
     * @return self
     */
    public function clearChildren()
    {
        $this->removeChildren($this->children);
        return $this;
    }

    /**
     * @param HierarchyInterface $child
     * @return bool
     */
    public function hasChild(HierarchyInterface $child)
    {
        foreach ($this->children as $entity) {
            if ($child === $entity) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return HierarchyInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }
}
