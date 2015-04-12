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

use Zend\View\Helper\AbstractHelper;

/**
 * View helper for rendering.
 */
class Nl2p extends AbstractHelper
{
    /**
     * @param string $string
     * @param bool $line_breaks
     * @param bool $xml
     * @return self|string
     */
    public function __invoke($string = null, $line_breaks = true, $xml = true)
    {
        if (func_num_args() === 0) {
            return $this;
        }

        return $this->simpleRender($string, $line_breaks, $xml);
    }

    /**
     * Render string
     *
     * @param string $string
     * @param bool $line_breaks
     * @param bool $xml
     * @return string
     */
    public function regExpRender($string, $line_breaks = true, $xml = false)
    {
        $string = str_replace(['<p>', '</p>', '<br>', '<br />'], '', $string);

        // It is conceivable that people might still want single line-breaks
        // without breaking into a new paragraph.
        if ($line_breaks === true) {
            return '<p>' . preg_replace(
                ["/([\n]{2,})/i", "/([^>])\n([^<])/i"],
                ["</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'],
                trim($string)
            ) . '</p>';
        } else {
            return '<p>' . preg_replace(
                ["/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"],
                ["</p>\n<p>", "</p>\n<p>", '$1<br' . ($xml == true ? ' /' : '') . '>$2'],
                trim($string)
            ) . '</p>';
        }
    }

    /**
     * Render string
     *
     * @param string $string
     * @return string
     */
    public function simpleRender($string)
    {
        $paragraphs = '';

        foreach (explode("\n", $string) as $line) {
            if (trim($line)) {
                $paragraphs .= '<p>' . $line . '</p>';
            }
        }

        return $paragraphs;
    }
}
