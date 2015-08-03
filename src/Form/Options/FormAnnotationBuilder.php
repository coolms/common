<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\Options;

use Zend\Stdlib\AbstractOptions;

class FormAnnotationBuilder extends AbstractOptions
{
    /**
     * An array of annotation builder's event listeners class names
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * An array of FQCN of extra annotations
     *
     * @var array
     */
    protected $annotations = [];

    /**
     * @var bool
     */
    protected $preserveDefinedOrder;

    /**
     * @var string
     */
    protected $cache = 'array';

    /**
     * @param  array $maps
     * @return self
     */
    public function setListeners(array $listeners)
    {
        $this->listeners = $listeners;
        return $this;
    }

    /**
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param array $annotations
     * @return self
     */
    public function setAnnotations(array $annotations)
    {
        $this->annotations = $annotations;
        return $this;
    }

    /**
     * @return array
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * @param bool $flag
     * @return self
     */
    public function setPreserveDefinedOrder($flag)
    {
        $this->preserveDefinedOrder = (bool) $flag;
    }

    /**
     * @return bool
     */
    public function getPreserveDefinedOrder()
    {
        return $this->preserveDefinedOrder;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setCache($name)
    {
        $this->cache = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCache()
    {
        return $this->cache;
    }
}
