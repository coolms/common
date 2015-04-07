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

use Zend\View\Helper\AbstractHtmlElement;

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
     * Invoke helper as functor
     *
     * @param mixed  $content
     * @param array  $attribs
     * @return string|AbstractHtmlContainer
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
            return (string) $content;
        }

        return $this->getOpenTag($tagName, $attribs) . $content . $this->getCloseTag($tagName);
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
}
