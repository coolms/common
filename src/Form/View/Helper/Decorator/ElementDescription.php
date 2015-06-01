<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper\Decorator;

use Zend\Form\ElementInterface,
    Zend\Form\FormInterface;

class ElementDescription extends AbstractHtmlContainer
{
    /**
     * @var string
     */
    protected $tagName = 'p';

    /**
     * {@inheritDoc}
     */
    public function render(
        $content,
        array $attribs = [],
        ElementInterface $element = null,
        FormInterface $form = null
    ) {
        if (!$content && !($content = $element->getOption('description'))) {
            return '';
        }

        return parent::render($content, $attribs, $element, $form);
    }
}
