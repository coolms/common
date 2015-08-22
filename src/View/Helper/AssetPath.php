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

use Zend\Filter\FilterChain,
    Zend\Filter\FilterInterface,
    Zend\View\Helper\AbstractHelper,
    Zend\View\Helper\BasePath;

/**
 * AssetPath
 */
class AssetPath extends AbstractHelper
{
    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * @var string
     */
    protected $defaultPath = 'assets/%s';

    /**
     * @var BasePath
     */
    protected $basePathPlugin;

    /**
     * @param array|string  $asset
     * @param string        $moduleNamespace
     * @return array|string
     */
    public function __invoke($asset, $moduleNamespace = null)
    {
        if (null === $moduleNamespace) {
            $options = $this->getView()->layout()->getOptions();
            if (isset($options['namespace'])) {
                $moduleNamespace = $options['namespace'];
            }
        }

        $path = sprintf($this->defaultPath, $moduleNamespace ? $this->filter($moduleNamespace) : '');

        if (is_string($asset)) {
            return $this->createPath($asset, $path);
        }

        if (is_array($asset)) {
            return array_map([$this, 'createPath'], $asset, array_fill(0, count($asset), $path));
        }
    }

    /**
     * @param string $asset
     * @param string $path
     */
    protected function createPath($asset, $path)
    {
        $url = parse_url($asset);
        if (isset($url['host']) || $asset[0] === '/' || $asset[0] === '\\') {
            return $asset;
        }

        $basePath = $this->getBasePathPlugin();
        return $basePath(trim($path, '/\\') . '/' . ltrim($asset, '/\\'));
    }

    /**
     * @return BasePath
     */
    protected function getBasePathPlugin()
    {
        if (null === $this->basePathPlugin) {
            $this->basePathPlugin = $this->getView()->plugin('basePath');
        }

        return $this->basePathPlugin;
    }

    /**
     * @param FilterInterface $filter
     * @return self
     */
    public function setFilter(FilterInterface $filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @param string $namespace
     * @return string
     */
    protected function filter($namespace)
    {
        $namespace = trim($namespace, '\\');
        if ($pos = strpos($namespace, '\\')) {
            $namespace = substr($namespace, 0, $pos);
        }

        return $this->getFilter()->filter($namespace);
    }

    /**
     * @return FilterInterface
     */
    protected function getFilter()
    {
        if (null === $this->filter) {
            $this->setFilter(new FilterChain([
                'filters' => [
                    ['name' => 'WordCamelCaseToDash'],
                    ['name' => 'WordUnderscoreToDash'],
                    ['name' => 'WordSeparatorToDash'],
                    ['name' => 'StringToLower'],
                ],
            ]));
        }

        return $this->filter;
    }
}
