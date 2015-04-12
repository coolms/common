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

use Zend\Form\FormInterface,
    Zend\View\Renderer\RendererInterface;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
trait FormProviderTrait
{
    /**
     * @var string
     */
    protected $formKey = 'form';

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @param FormInterface $form
     * @return self
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        if (null === $this->form) {
            $key = $this->getFormKey();
            return $this->getView()->$key;
        }

        return $this->form;
    }

    /**
     * @param string $formKey
     * @return self
     */
    public function setFormKey($formKey)
    {
        $this->formKey = $formKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey;
    }

    /**
     * @return RendererInterface
     */
    abstract public function getView();
}
