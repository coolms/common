<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper\Navigation;

use Zend\Navigation\Page\AbstractPage,
    Zend\View\Helper\Navigation\Breadcrumbs as BreadcrumbsHelper;

class Breadcrumbs extends BreadcrumbsHelper
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var bool
     */
    protected $renderAsList = false;

    /**
     * @var bool
     */
    protected $skipDuplicates = false;

    /**
     * @var string
     */
    protected $liContainerClass;

    /**
     * {@inheritDoc}
     *
     * @param string $id
     */
    public function render($container = null)
    {
        if (!$this->getPartial()) {
            return $this->renderStraight($container);
        }

        return parent::render($container);
    }

    /**
     * {@inheritDoc}
     */
    public function renderStraight($container = null)
    {
        $this->parseContainer($container);
        if (null === $container) {
            $container = $this->getContainer();
        }

        // find deepest active
        if (!$active = $this->findActive($container)) {
            return '';
        }

        $active = $active['page'];

        // put the deepest active page last in breadcrumbs
        if ($this->getLinkLast()) {
            $html = $this->htmlify($active);
        } else {
            /* @var $escaper \Zend\View\Helper\EscapeHtml */
            $escaper = $this->view->plugin('escapeHtml');
            $html = $escaper(
                $this->translate($active->getLabel(), $active->getTextDomain())
            );
        }

        if ($this->getRenderAsList()) {
            $attribs = [];
            if ($this->liContainerClass) {
                $attribs['class'] = $this->liContainerClass;
            }

            $attribString = $this->htmlAttribs($attribs);
            $html = "<li$attribString>$html</li>";
        }

        // walk back to root
        while ($parent = $active->getParent()) {
            if ($parent instanceof AbstractPage) {
                if (!$parent->getHref() ||
                    $this->getSkipDuplicates() &&
                    $parent->getParent() !== $container &&
                    $parent->getParent()->getHref() === $parent->getHref()
                ) {
                    $active = $parent;
                    continue;
                }

                // prepend crumb to html
                if ($this->getRenderAsList()) {
                    $html = "<li$attribString>{$this->htmlify($parent)}</li>$html";
                } else {
                    $html = $this->htmlify($parent)
                        . $this->getSeparator()
                        . $html;
                }
            }

            if ($parent === $container) {
                // at the root of the given container
                break;
            }

            $active = $parent;
        }

        $html = strlen($html) ? $this->getIndent() . $html : '';

        if ($this->getRenderAsList()) {
            $class  = $this->getClass();
            $id     = $this->getId();
            $html   = "<ol{$this->htmlAttribs(compact('class', 'id'))}>$html</ol>";
        }

        return $html;
    }

    /**
     * {@inheritDoc}
     */
    public function htmlify(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false)
    {
        $renderer = $this->getView();
        if ($partial = $page->get('partial')) {
            return $renderer->partial($partial, compact('page', 'escapeLabel', 'addClassToListItem'));
        }

        // get attribs for element
        $attribs = ['id' => $page->getId()];

        if ($title = $page->getTitle()) {
            $attribs['title'] = $title = $this->translate($title, $page->getTextDomain());
        }

        if ($addClassToListItem === false) {
            if (!empty($attribs['class'])) {
                $attribs['class'] .= " {$page->getClass()}";
            } else {
                $attribs['class']  = $page->getClass();
            }
        }

        if ($label = $page->getLabel()) {
            $label = $this->translate($label, $page->getTextDomain());
        }

        $html = '';
        if ($label) {
            if ($escapeLabel === true) {
                /* @var $escaper \Zend\View\Helper\EscapeHtml */
                $escaper = $this->view->plugin('escapeHtml');
                $html .= $escaper($title ?: $label);
            } else {
                $html .= $title ?: $label;
            }
        }

        // does page have a href
        if ($href = $page->getHref()) {
            $element = 'a';
            $attribs['href']    = $page->get('uri') ?: $href;
            $attribs['target']  = $page->getTarget();
        } else {
            $element = 'span';
        }

        $html = "<$element{$this->htmlAttribs($attribs)}>$html</$element>";

        return $html;
    }

    /**
     * @param bool $flag
     * @return self
     */
    public function setRenderAsList($flag = true)
    {
        $this->renderAsList = (bool) $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function getRenderAsList()
    {
        return $this->renderAsList;
    }

    /**
     * @param bool $flag
     * @return self
     */
    public function setSkipDuplicates($flag = true)
    {
        $this->skipDuplicates = (bool) $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSkipDuplicates()
    {
        return $this->skipDuplicates;
    }

    /**
     * Sets CSS id to use for 'ol' element which has children
     *
     * @param  string $id CSS id to set
     * @return self
     */
    public function setId($id)
    {
        if (is_string($id)) {
            $this->id = $id;
        }

        return $this;
    }

    /**
     * Returns CSS id to use for 'ol' element which has children
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets CSS class to use for 'ol' element which has children
     *
     * @param  string $class CSS class to set
     * @return self
     */
    public function setClass($class)
    {
        if (is_string($class)) {
            $this->class = $class;
        }

        return $this;
    }

    /**
     * Returns CSS class to use for 'ol' element which has children
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets CSS class to use for 'li' element which has children
     *
     * @param  string $class CSS class to set
     * @return self
     */
    public function setLiContainerClass($class)
    {
        if (is_string($class)) {
            $this->liContainerClass = $class;
        }

        return $this;
    }

    /**
     * Returns CSS class to use for 'li' element which has children
     *
     * @return string
     */
    public function getLiContainerClass()
    {
        return $this->liContainerClass;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        try {
            $markup = parent::__toString();
        } catch (\Exception $e) {
            $markup = $e->getMessage();
        }

        return $markup;
    }
}
