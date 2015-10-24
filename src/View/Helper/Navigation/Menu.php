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

use RecursiveIteratorIterator,
    Zend\Navigation\AbstractContainer,
    Zend\Navigation\Page\AbstractPage,
    Zend\View\Helper\Navigation\Menu as ZendMenu,
    CmsCommon\View\Helper\Decorator\Decorator;

class Menu extends ZendMenu
{
    /**
     * @var string
     */
    protected $decoratorNamespace = Decorator::OPTION_KEY;

    /**
     * {@inheritDoc}
     */
    protected function renderNormalMenu(
        AbstractContainer $container,
        $ulClass,
        $indent,
        $minDepth,
        $maxDepth,
        $onlyActive,
        $escapeLabels,
        $addClassToListItem,
        $liActiveClass
    ) {
        $html = '';

        // find deepest active
        $found = $this->findActive($container, $minDepth, $maxDepth);
        /* @var $escaper \Zend\View\Helper\EscapeHtmlAttr */
        $escaper = $this->view->plugin('escapeHtmlAttr');

        if ($found) {
            $foundPage  = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator(
            $container,
            RecursiveIteratorIterator::SELF_FIRST
        );
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibility
                continue;
            } elseif ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } elseif ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages(!$this->renderInvisible) ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                                // accept if active page has no children, or the
                                // children are too deep to be rendered
                                $accept = true;
                            }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);

            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass) {
                    $ulClass = ' class="' . $escaper($ulClass) . '"';
                } else {
                    $ulClass = '';
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . PHP_EOL;
            } elseif ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . PHP_EOL;
                    $html .= $ind . '</ul>' . PHP_EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            }

            // render li tag and page
            $liClasses = array();
            // Is page active?
            if ($isActive) {
                $liClasses[] = $liActiveClass;
            }
            // Add CSS class from page to <li>
            if ($addClassToListItem && $page->getClass()) {
                $liClasses[] = $page->getClass();
            }
            $liClass = empty($liClasses) ? '' : ' class="' . $escaper(implode(' ', $liClasses)) . '"';

            $html .= $myIndent . '    <li' . $liClass . '>' . PHP_EOL
                . $myIndent . '        ' . $this->htmlify($page, $escapeLabels, $addClassToListItem) . PHP_EOL;

            $ulClass = $page->get('ul_class');

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth+1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i-1);
                $html .= $myIndent . '    </li>' . PHP_EOL
                . $myIndent . '</ul>' . PHP_EOL;
            }
            $html = rtrim($html, PHP_EOL);
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
            $attribs['title'] = $this->translate($title, $page->getTextDomain());
        }

        if ($pageAttribs = $page->get('attribs')) {
            $attribs = array_merge($pageAttribs, $attribs);
        }

        if ($addClassToListItem === false) {
            if (!empty($attribs['class'])) {
                $attribs['class'] .= ' ' . $page->getClass();
            } else {
                $attribs['class'] = $page->getClass();
            }
        }

        if (($label = $page->get('label_helper')) && ($helper = $this->view->plugin($label))) {
            if (method_exists($helper, 'setTranslatorTextDomain')) {
                $helper->setTranslatorTextDomain($page->getTextDomain());
            }
            $label = $helper();
        } elseif ($label = $page->getLabel()) {
            $label = $this->translate($label, $page->getTextDomain());
        }

        $html = '';
        if ($label) {
            if ($escapeLabel === true) {
                /** @var \Zend\View\Helper\EscapeHtml $escaper */
                $escaper = $this->view->plugin('escapeHtml');
                $html .= $escaper($label);
            } else {
                $html .= $label;
            }
        }

        // does page have a href?
        $href = $page->getHref();
        if ($href) {
            $element = 'a';
            $attribs['href'] = $page->get('uri') ?: $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        if ($page->{$this->decoratorNamespace}) {
            $html = $renderer->decorator($html, $page->{$this->decoratorNamespace});
        }

        $html = '<' . $element . $this->htmlAttribs($attribs) . '>' . $html . '</' . $element . '>';
        return $html;
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
