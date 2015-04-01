<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\ServiceManager;

use Zend\ServiceManager\AbstractPluginManager as ZendAbstractPluginManager;

/**
 * Abstract plugin manager for doamin services.
 */
abstract class AbstractPluginManager extends ZendAbstractPluginManager
{
    /**
     * Don't share containers by default
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * @var array map of characters to be replaced through strtr
     */
    protected $canonicalNamesReplacements = ['-' => '\\', '_' => '\\', ' ' => '\\', '/' => '\\'];

    /**
     * Override: do not use peering service managers
     *
     * @param  string|array $name
     * @param  bool         $checkAbstractFactories
     * @param  bool         $usePeeringServiceManagers
     * @return bool
     */
    public function has($name, $checkAbstractFactories = true, $usePeeringServiceManagers = false)
    {
        return parent::has($name, $checkAbstractFactories, $usePeeringServiceManagers);
    }

    /**
     * Override: do not use peering service managers
     *
     * @param  string $name
     * @param  array $options
     * @param  bool $usePeeringServiceManagers
     * @return mixed
     */
    public function get($name, $options = [], $usePeeringServiceManagers = false)
    {
        if (is_string($options)) {
            $options = ['class_name' => $options];
        }

        return parent::get($name, $options, $usePeeringServiceManagers);
    }

    /**
     * {@inheritDoc}
     */
    protected function canonicalizeName($name)
    {
        if (isset($this->canonicalNames[$name])) {
            return $this->canonicalNames[$name];
        }

        // this is just for performance instead of using str_replace
        return $this->canonicalNames[$name] = strtr($name, $this->canonicalNamesReplacements);
    }
}
