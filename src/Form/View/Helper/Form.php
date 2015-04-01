<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper;

use Zend\Form\FormInterface,
    Zend\Form\View\Helper\Form as ZendForm,
    Zend\View\Helper\AbstractHelper;

/**
 * View helper for rendering Form objects
 */
class Form extends ZendForm
{
    /**
     * The name of the default view helper that is used to render fieldsets.
     *
     * @var string
     */
    protected $defaultFieldsetHelper = 'formCollection';

    /**
     * The view helper used to render sub fieldsets.
     *
     * @var AbstractHelper
     */
    protected $fieldsetHelper;

    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @return Form
     */
    public function __invoke(FormInterface $form = null, $renderMode = null)
    {
        if (!$form) {
            return $this;
        }

        return $this->render($form, $renderMode);
    }

    /**
     * Render a form from the provided $form,
     *
     * @param  FormInterface $form
     * @return string
     */
    public function render(FormInterface $form, $renderMode = null)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $fieldsetHelper = $this->getFieldsetHelper();
        if (method_exists($fieldsetHelper, 'setForm')) {
            $fieldsetHelper->setForm($form);
        }

        if (null === $renderMode) {
            $renderMode = $form->getOption('render_mode');
        }
        $rollbackRenderMode = null;
        if ($renderMode && $fieldsetHelper instanceof FormCollection) {
            $elementHelper = $this->view->plugin($fieldsetHelper->getDefaultElementHelper());
            if ($elementHelper instanceof FormRow) {
                $rollbackRenderMode = $elementHelper->getRenderMode();
                if ($rollbackRenderMode !== $renderMode) {
                    $elementHelper->setRenderMode($renderMode);
                }
            }
        }

        $markup = $this->openTag($form) . $fieldsetHelper($form, false, false) . $this->closeTag();

        if ($rollbackRenderMode) {
            $elementHelper->setRenderMode($rollbackRenderMode);
        }

        return $markup;
    }

    /**
     * Sets the fieldset helper that should be used by this collection.
     *
     * @param  AbstractHelper $fieldsetHelper The fieldset helper to use.
     * @return self
     */
    public function setFieldsetHelper(AbstractHelper $fieldsetHelper)
    {
    	$this->fieldsetHelper = $fieldsetHelper;

    	return $this;
    }

    /**
     * Retrieve the fieldset helper.
     *
     * @return AbstractHelper
     * @throws \RuntimeException
     */
    protected function getFieldsetHelper()
    {
    	if ($this->fieldsetHelper) {
    	    return $this->fieldsetHelper;
    	}

    	if (method_exists($this->view, 'plugin')) {
    	    $this->fieldsetHelper = $this->view->plugin($this->defaultFieldsetHelper);
    	}

    	if (!$this->fieldsetHelper instanceof AbstractHelper) {
    	    // @todo Ideally the helper should implement an interface.
    	    throw new \RuntimeException('Invalid fieldset helper set in Form. '
    	        . 'The helper must be an instance of AbstractHelper.');
    	}

    	return $this->fieldsetHelper;
    }
}
