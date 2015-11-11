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
    CmsCommon\Mapping\Hierarchy\HierarchyInterface,
    CmsCommon\Persistence\HierarchyMapperInterface,
    CmsCommon\Persistence\MapperProviderTrait,
    CmsCommon\Persistence\MapperProviderInterface;

/**
 * View helper for rendering objects hierarchy
 *
 * @property HierarchyMapperInterface $mapper
 * @method HierarchyMapperInterface getMapper()
 */
class Hierarchy extends AbstractHelper implements MapperProviderInterface
{
    use MapperProviderTrait;

    /**
     * @var bool
     */
    protected $direct = false;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var bool
     */
    protected $includeNode = false;

    /**
     * __construct
     *
     * @param HierarchyMapperInterface $mapper
     */
    public function __construct(HierarchyMapperInterface $mapper)
    {
        $this->setMapper($mapper);
    }

    /**
     * @param HierarchyInterface $node
     * @param array|bool $criteria
     * @param array $options
     * @param bool $includeNode
     * @return self|string
     */
    public function __invoke(
        HierarchyInterface $node = null,
        $criteria = null,
        $options = [],
        $includeNode = null
    ) {
        if (0 === func_num_args()) {
            return $this;
        }

        return $this->render($node, $criteria, $options, $includeNode);
    }

    /**
     * Render node's hierarchy
     *
     * @param HierarchyInterface $node
     * @param array|bool $criteria
     * @param array $options
     * @param bool $includeNode
     * @return string
     */
    public function render(HierarchyInterface $node = null, $criteria = null, $options = [], $includeNode = null)
    {
        return $this->getMapper()->childrenHierarchy(
            $node, // if node is null starting from root nodes
            null === $criteria ? $this->direct() : $criteria, // true: load all children, false: only direct, array: criteria
            array_replace_recursive($this->getOptions(), $options),
            null === $includeNode ? $this->includeNode() : $includeNode
        );
    }

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = [])
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @return null|mixed
     */
    public function getOption($name)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
    }

    /**
     * @param bool $flag
     * @return self
     */
    public function setDirect($flag)
    {
        $this->direct = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    protected function direct()
    {
        return $this->direct;
    }

    /**
     * @param bool $flag
     * @return self
     */
    public function setIncludeNode($flag)
    {
        $this->includeNode = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    protected function includeNode()
    {
        return $this->includeNode;
    }
}
