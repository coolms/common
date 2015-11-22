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

class Img extends HtmlContainer
{
    /**
     * @var string
     */
    protected $tagName = 'img';

    /**
     * @var string
     */
    protected $closeTag = null;

    /**
     * @var array
     */
    protected $attributes = [
        'alt' => '',
        'width' => 0,
        'height' => 0,
    ];

    /**
     * {@inheritDoc}
     */
    public function render($content, array $attribs = [])
    {
        if (is_string($content) && !array_key_exists('src', $attribs)) {
            $attribs['src'] = $content;
        }

        return parent::render(null, $attribs);
    }
}
