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

class A extends HtmlContainer
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * @var array
     */
    protected $attributes = [
        'href' => '#',
    ];
}
