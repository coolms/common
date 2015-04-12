<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper\Decorator;

use Zend\View\Helper\AbstractHelper,
    Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerAwareTrait,
    CmsCommon\View\Helper\HtmlContainer;

class Decorator extends AbstractHelper implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    const PLACEMENT_APPEND  = 'append';
    const PLACEMENT_PREPEND = 'prepend';

    const OPTION_KEY = 'decorators';

    /**
     * @var int
     */
    protected $orderStep = 10;

    /**
     * @param string $markup
     * @param array $decorators
     */
    public function __invoke($markup = null, array $decorators = [])
    {
        if (func_num_args() === 0) {
            return $this;
        }

        return call_user_func_array([$this, 'render'], func_get_args());
    }

    /**
     * @param string $markup
     * @param array $decorators
     * @return string
     */
    public function render($markup, array $decorators)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin') || !$decorators) {
            // Bail early, if renderer is not pluggable
            return $markup;
        }

        $param_arr = func_get_args();

        $decorators = $this->sort($decorators);
        foreach ($decorators as $decorator => $options)
        {
            if (!is_string($decorator)) {
                $decorator  = $options;
                $options    = [];
            }

            if (!is_array($options)) {
                continue;
            }

            if (isset($options['placement']) && $options['placement'] === false) {
                continue;
            }

            if (!empty($options['type'])) {
                $decorator = $options['type'];
            }

            $plugin = $this->getDecoratorHelper($decorator);

            if (isset($options['content'])) {
                $param_arr[0] = $options['content'];
            } else {
                $param_arr[0] = '';
            }

            if (isset($options['attributes'])) {
                $param_arr[1] = (array) $options['attributes'];
                unset($options['attributes']);
            } else {
                $param_arr[1] = $options;
                unset(
                    $param_arr[1]['content'],
                    $param_arr[1][self::OPTION_KEY],
                    $param_arr[1]['order'],
                    $param_arr[1]['placement'],
                    $param_arr[1]['type']
                );
            }

            if (!array_key_exists('placement', $options) && $plugin instanceof PlacedDecoratorInterface) {
                $options['placement'] = $plugin->getPlacement();
            }

            if (isset($options['placement'])) {
                $param_arr[0] = call_user_func_array($plugin, $param_arr);
                if (!empty($options[self::OPTION_KEY])) {
                    $param_arr[1] = $options[self::OPTION_KEY];
                    $param_arr[0] = call_user_func_array([$this, 'render'], $param_arr);
                }

                switch ($options['placement']) {
                    case self::PLACEMENT_APPEND:
                        $markup .= $param_arr[0];
                        break;
                    case self::PLACEMENT_PREPEND:
                        $markup  = $param_arr[0] . $markup;
                        break;
                }
            } else {
                $param_arr[0] = $markup;
                if (!empty($options[self::OPTION_KEY])) {
                    $param_arr[0] = call_user_func_array($plugin, $param_arr);
                    $param_arr[1] = $options[self::OPTION_KEY];
                    $markup = call_user_func_array([$this, 'render'], $param_arr);
                } else {
                    $markup = call_user_func_array($plugin, $param_arr);
                }
            }
        }

        return $markup;
    }

    /**
     * Sorts decorators according to 'order' option
     *
     * @param array $decorators
     * @return array
     */
    protected function sort(array $decorators)
    {
        $order   = 0;
        $sortAux = [];

        foreach ($decorators as $decorator => $options) {
            if (!is_string($decorator)) {
                $decorator  = $options;
                $options    = [];
            }

            if (!is_array($options)) {
                continue;
            }

            if (!empty($options['type'])) {
                $decorator = $options['type'];
            }

            if (!isset($options['order'])) {
                $plugin = $this->getDecoratorHelper($decorator);
                if ($plugin instanceof OrderedDecoratorInterface) {
                    $order = $plugin->getOrder();
                }
                $options['order'] = $order;
            } else {
                $order = (int) $options['order'];
            }

            $sortAux[] = $options['order'];
            $order += $this->orderStep;
        }

        array_multisort($sortAux, SORT_ASC, SORT_NUMERIC, $decorators);

        return $decorators;
    }

    /**
     * @param string $name
     * @throws \RuntimeException
     * @return HtmlContainer
     */
    protected function getDecoratorHelper($name)
    {
        $plugin = $this->getView()->plugin($name);
        if (!$plugin instanceof HtmlContainer) {
            throw new \RuntimeException(sprintf(
                'Decorator plugin must be of type CmsCommon\View\Helper\HtmlContainer; %s given',
                is_object($plugin) ? get_class($plugin) : gettype($plugin)
            ));
        }

        return $plugin;
    }
}
