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

use Zend\View\Helper\AbstractHtmlElement,
    Zend\View\Helper\EscapeHtml;

class HtmlContainer extends AbstractHtmlElement
{
    /**
     * @var array seed attributes
     */
    protected $attributes = [];

    /**
     * @var string default HTML container class
     */
    protected $defaultClass = '';

    /**
     * @var string
     */
    protected $tagName = 'div';

    /**
     * @var string
     */
    protected $openTag = '<%s%s>';

    /**
     * @var string
     */
    protected $closeTag = '</%s>';

    /**
     * @var EscapeHtml
     */
    protected $escapeHtmlHelper;

    /**
     * Invoke helper as functor
     *
     * @param mixed  $content
     * @param array  $attribs
     * @return string|self
     */
    public function __invoke($content = null, array $attribs = [])
    {
        if (0 === func_num_args()) {
            return $this;
        }

        return $this->render($content, $attribs);
    }

    /**
     * Render HTML container
     *
     * @param mixed $content
     * @param array $attribs
     * @return string
     */
    public function render($content, array $attribs = [])
    {
        if (isset($attribs['tagName'])) {
            $tagName = $attribs['tagName'];
            unset($attribs['tagName']);
        } else {
            $tagName = $this->getTagName();
        }

        if (!$tagName) {
            return $this->renderContent($content);
        }

        if (!$content && !$this->closeTag) {
            $openTag = trim($this->getOpenTag($tagName, $attribs));
            return substr_replace($openTag, $this->getClosingBracket(), -1);
        }

        return $this->getOpenTag($tagName, $attribs) .
            $this->renderContent($content) .
            $this->getCloseTag($tagName);
    }

    /**
     * @param mixed $content
     * @todo Use type view helpers
     * @return string
     */
    private function renderContent($content)
    {
        if (is_string($content)) {
            return $content;
        }

        if (is_array($content)) {
            $content = array_map('strval', $content);
            return implode("\n", $content);
        }

        if (is_object($content) && method_exists($content, '__toString')) {
            return (string) $content;
        }

        return '';
    }

    /**
     * @param string $tagName
     * @return self
     */
    public function setTagName($tagName)
    {
        $this->tagName = $tagName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @param string $openTag
     * @return self
     */
    public function setOpenTagPattern($openTag)
    {
        $this->openTag = $openTag;
        return $this;
    }

    /**
     * @param string $tagName
     * @param array $attribs
     * @return string
     */
    protected function getOpenTag($tagName, array $attribs = [])
    {
        $attribs = $this->mergeAttributes($attribs);
        return sprintf($this->openTag, $tagName, $this->htmlAttribs($attribs));
    }

    /**
     * @param string $closeTag
     * @return self
     */
    public function setCloseTagPattern($closeTag)
    {
        $this->closeTag = $closeTag;
        return $this;
    }

    /**
     * @return string
     */
    protected function getCloseTag($tagName)
    {
        return sprintf($this->closeTag, $tagName);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $attrib
     * @return mixed
     */
    public function getAttribute($attrib)
    {
        if (isset($this->attributes[$attrib])) {
            return $this->attributes[$attrib];
        }
    }

    /**
     * @param array $attribs
     * @return self
     */
    public function setAttributes(array $attribs)
    {
        foreach ($attribs as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * @param array $attribs
     * @return array
     */
    protected function mergeAttributes(array $attribs)
    {
        if ($this->defaultClass) {
            $attribs = array_merge_recursive(['class' => $this->defaultClass], $attribs);
            if (is_array($attribs['class'])) {
                $attribs['class'] = implode(' ', $attribs['class']);
            }
        }

        return array_replace_recursive($this->getAttributes(), $attribs);
    }

    /**
     * Retrieve the escapeHtml helper
     *
     * @return EscapeHtml
     */
    protected function getEscapeHtmlHelper()
    {
        if ($this->escapeHtmlHelper) {
            return $this->escapeHtmlHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->escapeHtmlHelper = $this->view->plugin('escapehtml');
        }

        if (!$this->escapeHtmlHelper instanceof EscapeHtml) {
            $this->escapeHtmlHelper = new EscapeHtml();
            $this->escapeHtmlHelper->setView($this->getView());
        }

        return $this->escapeHtmlHelper;
    }
}
