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
    const OPTION_KEY        = 'decorators';

    /**
     * @param string $markup
     * @param array $decorators
     */
    public function __invoke($markup = null, array $decorators = [])
    {
        if (func_num_args() === 0) {
            return $this;
        }

        return $this->render($markup, $decorators);
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

        foreach ($decorators as $decorator => $options)
        {
            if (!is_string($decorator)) {
                $decorator  = $options;
                $options    = [];
            }

            if (!empty($options['type'])) {
                $decorator = $options['type'];
            }

            if (!(($plugin = $renderer->plugin($decorator))
                && $plugin instanceof HtmlContainer)
            ) {
                continue;
            }

            if (isset($options['content'])) {
                $content = $options['content'];
                unset($options['content']);
            } else {
                $content = '';
            }

            if (isset($options['attributes'])) {
                $attributes = (array) $options['attributes'];
                unset($options['attributes']);
            } else {
                $attributes = $options;
            }

            if (isset($options['placement'])) {

                unset($attributes['placement'], $attributes[self::OPTION_KEY]);
                $content = $plugin($content, $attributes);
                if (!empty($options[self::OPTION_KEY])) {
                    $content = $this->render($content, $options[self::OPTION_KEY]);
                }

                switch ($options['placement']) {
                    case self::PLACEMENT_APPEND:
                        $markup .= $content;
                        break;
                    case self::PLACEMENT_PREPEND:
                        $markup = $content . $markup;
                        break;
                }

            } else {
                $markup = $plugin($markup, $attributes);
            }
        }

        return $markup;
    }
}
