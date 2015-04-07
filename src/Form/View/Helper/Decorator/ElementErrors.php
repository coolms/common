<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper\Decorator;

use Zend\Form\ElementInterface,
    Zend\Form\FormInterface,
    Zend\Form\LabelAwareInterface,
    Zend\Form\View\Helper\FormElementErrors;

class ElementErrors extends AbstractHtmlContainer
{
    /**
     * @var string
     */
    protected $tagName = null;

    /**
     * @var FormElementErrors
     */
    protected $formElementErrorsHelper;

    /**
     * {@inheritDoc}
     */
    public function render($content, array $attribs = [], ElementInterface $element = null, FormInterface $form = null)
    {
        if (!$this->isElementHasError($element, $form)) {
            return '';
        }

        if (!$content) {
            $formElementErrorsHelper = $this->getFormElementErrorsHelper();
            $content = $formElementErrorsHelper($element, $this->mergeAttributes($attribs));
        }

        return parent::render($content, $attribs, $element, $form);
    }

    /**
     * @return FormElementErrors
     */
    protected function getFormElementErrorsHelper()
    {
        if ($this->formElementErrorsHelper) {
            return $this->formElementErrorsHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->formElementErrorsHelper = $this->view->plugin('form_element_errors');
        }

        if (!$this->formElementErrorsHelper instanceof FormElementErrors) {
            $this->formElementErrorsHelper = new FormElementErrors();
        }

        return $this->formElementErrorsHelper;
    }
}
