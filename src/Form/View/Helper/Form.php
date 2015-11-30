<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\View\Helper;

use Zend\Filter\Word\CamelCaseToDash,
    Zend\Form\FormInterface,
    Zend\Form\View\Helper\Form as FormHelper,
    Zend\View\Helper\AbstractHelper,
    CmsCommon\View\Exception\InvalidHelperException;

/**
 * View helper for rendering Form objects
 */
class Form extends FormHelper
{
    /**
     * @var string default form class
     */
    private $defaultClass = 'cms-form';

    /**
     * The name of the default view helper that is used to render fieldsets.
     *
     * @var string
     */
    protected $defaultFieldsetHelper = 'formCollection';

    /**
     * The view helper used to render form elements.
     *
     * @var AbstractHelper
     */
    protected $fieldsetHelper;

    /**
     * The name of the default view helper that is used to render form messages.
     *
     * @var string
     */
    protected $defaultFormMessagesHelper = 'formMessages';

    /**
     * The view helper used to render form messages.
     *
     * @var AbstractHelper
     */
    protected $formMessagesHelper;

    /**
     * @var array map of name parts to be replaced through str_replace
     */
    protected $classNameReplacements = [
        '\\Document\\'      => '\\Form\\',
        '\\Entity\\'        => '\\Form\\',
        '\\ValueObject\\'   => '\\Form\\',
        '\\ValueObjects\\'  => '\\Form\\',
    ];

    /**
     * Invoke as function
     *
     * @param  null|FormInterface $form
     * @param  string $renderMode
     * @return self|string
     */
    public function __invoke(FormInterface $form = null, $renderMode = null)
    {
        if (!$form) {
            return $this;
        }

        return $this->render($form, $renderMode);
    }

    /**
     * Render a form from the provided $form
     *
     * @param  FormInterface $form
     * @param  string $renderMode
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

        $formMessagesHelper = $this->getFormMessagesHelper();

        $markup = $this->openTag($form)
            . $formMessagesHelper($form)
            . $fieldsetHelper($form, false, false)
            . $this->closeTag();

        if ($rollbackRenderMode) {
            $elementHelper->setRenderMode($rollbackRenderMode);
        }

        return $markup;
    }

    /**
     * {@inheritDoc}
     */
    public function openTag(FormInterface $form = null)
    {
        if ($form) {
            $class = $form->getAttribute('class');
            if (strpos($class, $this->defaultClass) === false) {
                $class = trim("$class {$this->defaultClass}");
            }

            if ($object = $form->getObject()) {
                $className = str_replace(
                    array_keys($this->classNameReplacements),
                    array_values($this->classNameReplacements),
                    get_class($object)
                );
            } else {
                $className = get_class($form);
            }

            $parts = explode('\\', $className);
            foreach ($parts as $part) {
                $classes[] = strtolower($part);
                if (count($classes) > 1) {
                    $class .= ' ' . implode('-', $classes);
                }
            }

            $form->setAttribute('class', $class);

            $formAttributes = $form->getAttributes();
            if (!array_key_exists('id', $formAttributes) && $classes) {
                $form->setAttribute('id', implode('-', $classes));
            }
        }

        return parent::openTag($form);
    }

    /**
     * Sets the fieldset helper that should be used by this collection
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
     * Retrieve the fieldset helper
     *
     * @return AbstractHelper
     * @throws InvalidHelperException
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
    	    throw InvalidHelperException::invalidHelperInstance($this->fieldsetHelper);
    	}

    	return $this->fieldsetHelper;
    }

    /**
     * Sets the form messages helper
     *
     * @param  AbstractHelper $formMessagesHelper The form messages helper to use.
     * @return self
     */
    public function setFormMessagestHelper(AbstractHelper $formMessagesHelper)
    {
        $this->formMessagesHelper = $formMessagesHelper;
        return $this;
    }

    /**
     * Retrieve the form messages helper
     *
     * @return AbstractHelper
     * @throws InvalidHelperException
     */
    protected function getFormMessagesHelper()
    {
        if ($this->formMessagesHelper) {
            return $this->formMessagesHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->formMessagesHelper = $this->view->plugin($this->defaultFormMessagesHelper);
        }

        if (!$this->formMessagesHelper instanceof AbstractHelper) {
            // @todo Ideally the helper should implement an interface.
            throw InvalidHelperException::invalidHelperInstance($this->formMessagesHelper);
        }

        return $this->formMessagesHelper;
    }
}
