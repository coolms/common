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
    Zend\Form\FormInterface,
    CmsCommon\View\Helper\HtmlContainer;

class Fieldset extends HtmlContainer
{
    /**
     * @var string
     */
    protected $tagName = 'fieldset';

    /**
     * @var Legend
     */
    protected $legendHelper;

    /**
     * {@inheritDoc}
     *
     * @param  ElementInterface $element
     * @param  FormInterface $form
     */
    public function __invoke(
        $content = null,
        array $attribs = [],
        ElementInterface $element = null,
        FormInterface $form = null
    ) {
        if (func_num_args() === 0) {
            return $this;
        }

        return $this->render($content, $attribs, $element, $form);
    }

    /**
     * {@inheritDoc}
     *
     * @param  ElementInterface $element
     * @param  FormInterface $form
     */
    public function render(
        $content,
        array $attribs = [],
        ElementInterface $element = null,
        FormInterface $form = null
    ) {
        $legendHelper = $this->getLegendHelper();
        $content = $legendHelper(null, [], $element, $form) . $content;

        return parent::render($content, $attribs);
    }

    /**
     * @return Legend
     */
    protected function getLegendHelper()
    {
        if ($this->legendHelper) {
            return $this->legendHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->legendHelper = $this->view->plugin('legend');
        }

        if (!$this->legendHelper instanceof Legend) {
            $this->legendHelper = new Legend();
            $this->legendHelper->setView($this->getView());
        }

        return $this->legendHelper;
    }
}
