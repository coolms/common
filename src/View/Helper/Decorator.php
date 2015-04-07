<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper;

use Zend\View\Helper\AbstractHelper,
    CmsCommon\View\Helper\HtmlContainer;

class Decorator extends AbstractHelper
{
    const PLACEMENT_APPEND  = 'append';
    const PLACEMENT_PREPEND = 'prepend';

    const OPTION_KEY = 'decorators';

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

        foreach ($decorators as $decorator => $options)
        {
            if ($options === false) {
                continue;
            }

            if (!is_string($decorator)) {
                $decorator  = $options;
                $options    = [];
            }

            if (!empty($options['type'])) {
                $decorator = $options['type'];
                unset($options['type']);
            }

            if (!(($plugin = $renderer->plugin($decorator))
                && $plugin instanceof HtmlContainer)
            ) {
                continue;
            }

            if (isset($options['content'])) {
                if ($options['content'] === false) {
                    continue;
                }
                $param_arr[0] = $options['content'];
                unset($options['content']);
            } else {
                $param_arr[0] = '';
            }

            if (isset($options['attributes'])) {
                $param_arr[1] = (array) $options['attributes'];
                unset($options['attributes']);
            } else {
                $param_arr[1] = $options;
                unset($param_arr[1]['placement'], $param_arr[1][self::OPTION_KEY]);
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
}
