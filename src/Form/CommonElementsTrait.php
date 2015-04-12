<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form;

use Zend\Captcha\AdapterInterface,
    Zend\Filter\FilterChain,
    Zend\Form\ElementInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

trait CommonElementsTrait
{
    use CommonOptionsTrait;

    /**
     * @var string
     */
    protected $captchaElementName;

    /**
     * @var string
     */
    protected $csrfElementName;

    /**
     * @var FilterChain
     */
    private $nameFilterChain;

    /**
     * Init common form elements
     */
    public function init()
    {
        if ($this->getUseCaptcha()) {
            $this->setupCaptchaElement();
        }

        if ($this->getFormTimeout()) {
            $this->setupCsrfElement();
        }

        if ($this->getUseSubmitElement()) {
            $this->setupSubmitElement();
        }

        if ($this->getUseResetElement()) {
            $this->setupResetElement();
        }
    }

    /**
     * @param  array|Traversable $options
     * @return self
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['form_label'])) {
            $this->setLabel($options['form_label']);
        }

        if (!empty($options['captcha_options'])) {
            $this->setCaptchaOptions($options['captcha_options']);
            if (!isset($options['use_captcha'])) {
                $options['use_captcha'] = true;
            }
        }

        if (isset($options['use_captcha'])) {
            $this->setUseCaptcha($options['use_captcha']);
        }

        if (isset($options['form_timeout'])) {
            $this->setFormTimeout($options['form_timeout']);
        }

        if (isset($options['use_submit_element'])) {
            $this->setUseSubmitElement($options['use_submit_element']);
        }

        if (isset($options['use_reset_element'])) {
            $this->setUseResetElement($options['use_reset_element']);
        }

        return $this;
    }

    /**
     * Does the fieldset have an element/fieldset by the given name?
     *
     * @param  string $elementOrFieldset
     * @return bool
     */
    public function has($elementOrFieldset)
    {
        if (parent::has($elementOrFieldset)) {
            return true;
        }

        if ($elementOrFieldset === 'captcha') {
            $elementOrFieldset = $this->getCaptchaElementName();
        }
        if ($elementOrFieldset === 'csrf') {
            $elementOrFieldset = $this->getCsrfElementName();
        }

        return parent::has($elementOrFieldset);
    }

    /**
     * Retrieve a named element or fieldset
     *
     * @param  string $elementOrFieldset
     * @return ElementInterface
     */
    public function get($elementOrFieldset)
    {
        if (!parent::has($elementOrFieldset)) {
            if ($elementOrFieldset === 'captcha') {
                $elementOrFieldset = $this->getCaptchaElementName();
            }
            if ($elementOrFieldset === 'csrf') {
                $elementOrFieldset = $this->getCsrfElementName();
            }
        }

        return parent::get($elementOrFieldset);
    }

    /**
     * @param bool $flag
     * @return self
     */
    public function setUseCaptcha($flag)
    {
        if ($flag) {
            $this->setupCaptchaElement();
        } elseif($this->has('captcha')) {
            $this->remove($this->get('captcha')->getName());
        }

        $this->useCaptcha = $flag;

        return $this;
    }

    /**
     * Setup Captcha protection
     */
    protected function setupCaptchaElement()
    {
        if (!$this->getCaptchaOptions() || $this->has('captcha')) {
            return;
        }

        $this->add(
            [
                'name' => $this->getCaptchaElementName(),
                'type' => 'Captcha',
                'attributes' => [
                    'required' => true,
                    'autocomplete' => 'off',
                ],
                'options' => [
                    'captcha' => $this->getCaptchaOptions(),
                    'label' => 'Verify you are human',
                    'text_domain' => 'default',
                ],
            ],
            ['priority' => -970]
        );
    }

    /**
     * @return string
     */
    protected function getCaptchaElementName()
    {
        if (null === $this->captchaElementName) {
            $name = $this->getName();
            if ($name) {
                $this->captchaElementName  = $this->getNameFilterChain()->filter($name);
                $this->captchaElementName .= '_captcha';
            } else {
                $this->captchaElementName  = 'captcha';
            }
        }

        return $this->captchaElementName;
    }

    /**
     * @return array|\Traversable|AdapterInterface
     */
    protected function getCaptchaOptions()
    {
        if (!$this->captchaOptions) {
            $services = $this->getFormFactory()->getFormElementManager()->getServiceLocator();
            if ($services instanceof ServiceLocatorInterface
                && $services->has('Zend\\Captcha\\AdapterInterface')
            ) {
                $this->setCaptchaOptions($services->get('Zend\\Captcha\\AdapterInterface'));
            }
        }

        return $this->captchaOptions;
    }

    /**
     * @param int $ttl
     * @return self
     */
    public function setFormTimeout($ttl)
    {
        if ($ttl) {
            $this->setupCsrfElement();
        } elseif($this->has('csrf')) {
            $this->remove($this->get('csrf')->getName());
        }

        $this->formTimeout = $ttl;

        return $this;
    }

    /**
     * Setup CSRF protection
     */
    protected function setupCsrfElement()
    {
        if ($this->has('csrf')) {
            return;
        }

        $this->add(
            [
                'name' => $this->getCsrfElementName(),
                'type' => 'Csrf',
                'options' => [
                    'csrf_options' => [
                        'timeout' => $this->getFormTimeout(),
                    ],
                ],
            ],
            ['priority' => -980]
        );
    }

    /**
     * @return string
     */
    protected function getCsrfElementName()
    {
        if (null === $this->csrfElementName) {
            $name = $this->getName();
            if ($name) {
                $this->csrfElementName  = $this->getNameFilterChain()->filter($name);
                $this->csrfElementName .= '_csrf';
            } else {
                $this->csrfElementName  = 'csrf';
            }
        }

        return $this->csrfElementName;
    }

    /**
     * @param bool $flag
     * @return self
     */
    public function setUseSubmitElement($flag)
    {
        if ($flag) {
            $this->setupSubmitElement();
        } elseif($this->has('submit')) {
            $this->remove($this->get('submit')->getName());
        }

        $this->useSubmitElement = $flag;

        return $this;
    }

    /**
     * Setup submit element
     */
    protected function setupSubmitElement()
    {
        if ($this->has('submit')) {
            return;
        }

        $this->add(
            [
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => [
                    'type' => 'submit',
                    'value' => 'Submit',
                ],
            ],
            ['priority' => -990]
        );
    }

    /**
     * @param bool $flag
     * @return self
     */
    public function setUseResetElement($flag)
    {
        if ($flag) {
            $this->setupResetElement();
        } elseif($this->has('reset')) {
            $this->remove($this->get('reset')->getName());
        }

        $this->useResetElement = $flag;

        return $this;
    }

    /**
     * Setup reset element
     */
    protected function setupResetElement()
    {
        if ($this->has('reset')) {
            return;
        }

        $this->add(
            [
                'name' => 'reset',
                'type' => 'Submit',
                'attributes' => [
                    'type'  => 'reset',
                    'value' => 'Reset',
                ],
            ],
            ['priority' => -1000]
        );
    }

    /**
     * @return FilterChain
     */
    private function getNameFilterChain()
    {
        if (null === $this->nameFilterChain) {
            $this->nameFilterChain = (new FilterChain)
                 ->attach(new \Zend\Filter\Word\SeparatorToSeparator('\\', ''))
                 ->attachByName('Word\CamelCaseToUnderscore')
                 ->attachByName('Word\DashToUnderscore');
        }

        return $this->nameFilterChain;
    }
}
