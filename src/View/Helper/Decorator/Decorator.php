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
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\I18n\Translator\TranslatorAwareTrait,
    Zend\Stdlib\ArrayUtils,
    CmsCommon\View\Helper\HtmlContainer;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

class Decorator extends AbstractHelper implements EventManagerAwareInterface, TranslatorAwareInterface
{
    use EventManagerAwareTrait,
        TranslatorAwareTrait;

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
     * @param array|\Traversable $decorators
     * @return string
     */
    public function render($markup, $decorators)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin') || !$decorators) {
            // Bail early, if renderer is not pluggable
            return $markup;
        }

        $param_arr = func_get_args();
        $decorators = call_user_func_array([$this, 'prepare'], $param_arr);
        foreach ($decorators as $decorator => $options) {
            if (isset($options['placement']) && $options['placement'] === false) {
                continue;
            }

            if (!empty($options['type'])) {
                $decorator = $options['type'];
            }

            $plugin = $this->getDecoratorHelper($decorator);
            $rollbackTextDomain = null;
            if ($plugin instanceof TranslatorAwareInterface) {
                $rollbackTextDomain = $plugin->getTranslatorTextDomain();
                if (!$rollbackTextDomain || $rollbackTextDomain === 'default') {
                    $plugin->setTranslatorTextDomain($this->getTranslatorTextDomain());
                }
            }

            $param_arr[0] = isset($options['content']) ? $options['content'] : '';

            if (isset($options['attributes'])) {
                $param_arr[1] = (array) $options['attributes'];
                unset($options['attributes']);
            } else {
                $param_arr[1] = $options;
                unset(
                    $param_arr[1]['content'],
                    $param_arr[1][static::OPTION_KEY],
                    $param_arr[1]['order'],
                    $param_arr[1]['placement'],
                    $param_arr[1]['type']
                );
            }

            if (!array_key_exists('placement', $options) &&
                $plugin instanceof PlacedDecoratorInterface
            ) {
                $options['placement'] = $plugin->getPlacement();
            }

            if (isset($options['placement'])) {
                $param_arr[0] = call_user_func_array($plugin, $param_arr);
                if (!empty($options[static::OPTION_KEY])) {
                    $param_arr[1] = $options[static::OPTION_KEY];
                    $param_arr[0] = call_user_func_array([$this, 'render'], $param_arr);
                }

                switch ($options['placement']) {
                    case static::PLACEMENT_APPEND:
                        $markup .= $param_arr[0];
                        break;
                    case static::PLACEMENT_PREPEND:
                        $markup  = $param_arr[0] . $markup;
                }

            } else {
                $param_arr[0] = $markup;
                if (!empty($options[static::OPTION_KEY])) {
                    $param_arr[0] = call_user_func_array($plugin, $param_arr);
                    $param_arr[1] = $options[static::OPTION_KEY];
                    $markup = call_user_func_array([$this, 'render'], $param_arr);
                } else {
                    $markup = call_user_func_array($plugin, $param_arr);
                }
            }

            if ($rollbackTextDomain) {
                $plugin->setTranslatorTextDomain($rollbackTextDomain);
            }
        }

        return $markup;
    }

    /**
     * Sorts decorators according to 'order' option
     *
     * @param string $markup
     * @param array|\Traversable $decorators
     * @return array
     */
    protected function prepare($markup, $decorators)
    {
        $param_arr = func_get_args();
        if (is_array($decorators)) {
            $param_arr[1] = new \ArrayObject($decorators);
        }

        $order = 0;
        $sortAux = [];

        foreach ($param_arr[1] as $decorator => $options) {
            if (!is_string($decorator) && is_string($options)) {
                $param_arr[1][$options] = [];
                continue;
            } elseif (is_callable($options)) {
                $options = call_user_func_array($options, $param_arr);
                $param_arr[1][$decorator] = $options;
            }

            if (!is_array($options)) {
                continue;
            }

            if (!isset($options['order'])) {
                $plugin = $this->getDecoratorHelper(
                    empty($options['type']) ? $decorator : $options['type']
                );

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

        $param_arr[1] = ArrayUtils::iteratorToArray($param_arr[1]);
        $param_arr[1] = array_filter($param_arr[1], 'is_array');
        array_multisort($sortAux, SORT_ASC, SORT_NUMERIC, $param_arr[1]);

        return $param_arr[1];
    }

    /**
     * @param string $name
     * @throws \RuntimeException
     * @return null|HtmlContainer
     */
    protected function getDecoratorHelper($name)
    {
        try {
            $plugin = $this->getView()->plugin($name);
        } catch (ServiceNotFoundException $e) {
            return;
        }

        if (!$plugin instanceof HtmlContainer) {
            throw new \RuntimeException(sprintf(
                'Decorator plugin must be of type %s; %s given',
                HtmlContainer::class,
                is_object($plugin) ? get_class($plugin) : gettype($plugin)
            ));
        }

        return $plugin;
    }
}
