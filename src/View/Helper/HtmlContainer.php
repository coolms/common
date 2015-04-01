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

class HtmlContainer extends AbstractHtmlContainer
{
    /**
     * Invoke helper as functor
     *
     * @param mixed  $content
     * @param array  $attribs
     * @return string|AbstractHtmlContainer
     */
    public function __invoke($content = null, array $attribs = [])
    {
        if (null === $content) {
            return $this;
        }

        return $this->render($content, $attribs);
    }

    /**
     * Render HTML container
     *
     * @param mixed $content
     * @param array $attribs
     */
    public function render($content, array $attribs = [])
    {
        if (isset($attribs['tagName'])) {
            $tagName = $attribs['tagName'];
            unset($attribs['tagName']);
        } else {
            $tagName = $this->getTagName();
        }

        return $this->getOpenTag($tagName, $attribs) . $content . $this->getCloseTag($tagName);
    }
}
