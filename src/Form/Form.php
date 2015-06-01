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
    Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerAwareTrait,
    Zend\Filter\FilterChain,
    Zend\Filter\Word\SeparatorToSeparator,
    Zend\Form\ElementInterface,
    Zend\Form\Element\Collection,
    Zend\Form\Exception\InvalidArgumentException,
    Zend\Form\FieldsetInterface,
    Zend\Form\Form as ZendForm,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\Stdlib\ArrayUtils,
    Zend\Stdlib\PriorityList;

class Form extends ZendForm implements
        FormInterface,
        EventManagerAwareInterface,
        ServiceLocatorAwareInterface
{
    use EventManagerAwareTrait,
        ServiceLocatorAwareTrait,
        CommonOptionsTrait {
            CommonOptionsTrait::getUseFormLabel as private;
            CommonOptionsTrait::setUseFormLabel as private;
            CommonOptionsTrait::getFormTimeout as private;
            CommonOptionsTrait::getUseCsrf as private;
            CommonOptionsTrait::getUseCaptcha as private;
            CommonOptionsTrait::getUseSubmitElement as private;
            CommonOptionsTrait::getUseResetElement as private;
        }

    /**
     * @var string
     */
    private $captchaElementName;

    /**
     * @var string
     */
    private $csrfElementName;

    /**
     * @var FilterChain
     */
    private $nameFilterChain;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this);

        parent::init();

        if ($this->getOption('use_captcha')) {
            $this->setupCaptchaElement();
        }

        if ($this->getOption('use_csrf')) {
            $this->setupCsrfElement();
        }

        if ($this->getOption('use_submit_element')) {
            $this->setupSubmitElement();
        }

        if ($this->getOption('use_reset_element')) {
            $this->setupResetElement();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['use_form_label'])) {
            $this->setUseFormLabel($options['use_form_label']);
        }

        if (array_key_exists('form_timeout', $options)) {
            $this->setFormTimeout($options['form_timeout']);
            if (null !== $options['form_timeout'] && !isset($options['use_csrf'])) {
                $options['use_csrf'] = true;
            }
        }

        if (isset($options['use_csrf'])) {
            $this->setUseCsrf($options['use_csrf']);
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

        if (isset($options['use_submit_element'])) {
            $this->setUseSubmitElement($options['use_submit_element']);
        }

        if (isset($options['use_reset_element'])) {
            $this->setUseResetElement($options['use_reset_element']);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setOption($option, $value)
    {
        switch ($option) {
            case 'use_form_label':
                $this->setUseFormLabel($value);
                break;
            case 'form_timeout':
                $this->setFormTimeout($value);
                break;
            case 'use_csrf':
                $this->setUseCsrf($value);
                break;
            case 'captcha_options':
                $this->setCaptchaOptions($value);
                break;
            case 'use_captcha':
                $this->setUseCaptcha($value);
                break;
            case 'use_submit_element':
                $this->setUseSubmitElement($value);
                break;
            case 'use_reset_element':
                $this->setUseResetElement($value);
                break;
        }

        return parent::setOption($option, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        if (!$this->getUseFormLabel()) {
            return '';
        }

        return parent::getLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {
        parent::setName($name);

        if ($this->has($this->getCaptchaElementName())) {
            $element = $this->get($this->getCaptchaElementName());

            $this->captchaElementName = null;
            $newName = $this->getCaptchaElementName();

            $flags['priority'] = $this->iterator->toArray(PriorityList::EXTR_PRIORITY)[$element->getName()];

            $this->remove($element->getName());
            $element->setName($newName);
            $this->add($element, $flags);
        }

        if ($this->has($this->getCsrfElementName())) {
            $element = $this->get($this->getCsrfElementName());

            $this->csrfElementName = null;
            $newName = $this->getCsrfElementName();

            $flags['priority'] = $this->iterator->toArray(PriorityList::EXTR_PRIORITY)[$element->getName()];

            $this->remove($element->getName());
            $element->getCsrfValidator()->setName($newName);
            $element->setName($newName);
            $this->add($element, $flags);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function has($elementOrFieldset)
    {
        if (parent::has($elementOrFieldset)) {
            return true;
        }

        if ($elementOrFieldset === 'captcha') {
            $elementOrFieldset = $this->getCaptchaElementName();
        } elseif ($elementOrFieldset === 'csrf') {
            $elementOrFieldset = $this->getCsrfElementName();
        }

        return parent::has($elementOrFieldset);
    }

    /**
     * {@inheritDoc}
     */
    public function get($elementOrFieldset)
    {
        if (!parent::has($elementOrFieldset)) {
            if ($elementOrFieldset === 'captcha') {
                $elementOrFieldset = $this->getCaptchaElementName();
            } elseif ($elementOrFieldset === 'csrf') {
                $elementOrFieldset = $this->getCsrfElementName();
            }
        }

        return parent::get($elementOrFieldset);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($elementOrFieldset)
    {
        if (!parent::has($elementOrFieldset)) {
            if ($elementOrFieldset === 'captcha') {
                $elementOrFieldset = $this->getCaptchaElementName();
            } elseif ($elementOrFieldset === 'csrf') {
                $elementOrFieldset = $this->getCsrfElementName();
            }
        }

        return parent::remove($elementOrFieldset);
    }

    /**
     * Set the validation group (set of values to validate)
     *
     * Typically, proxies to the composed input filter
     *
     * @throws InvalidArgumentException
     * @return self
     */
    public function setElementGroup()
    {
        $argc = func_num_args();
        if (0 === $argc) {
            throw new InvalidArgumentException(sprintf(
                '%s expects at least one argument; none provided',
                __METHOD__
            ));
        }

        $argv = func_get_args();
        $group = $argc > 1 ? $argv : array_shift($argv);

        if ($this->has($this->getCaptchaElementName())) {
            $name = $this->getCaptchaElementName();
            if (!in_array($name, $group)) {
                $group[] = $name;
            }
        }

        if ($this->has($this->getCsrfElementName())) {
            $name = $this->getCsrfElementName();
            if (!in_array($name, $group)) {
                $group[] = $name;
            }
        }

        if ($this->has('submit')) {
            if (!in_array('submit', $group)) {
                $group[] = 'submit';
            }
        }

        if ($this->has('reset')) {
            if (!in_array('reset', $group)) {
                $group[] = 'reset';
            }
        }

        $this->removeFieldsetElementGroup($group, $this);
        $this->setValidationGroup($group);

        return $this;
    }

    /**
     * @param array $group
     * @param FieldsetInterface $interface
     * @return void
     */
    private function removeFieldsetElementGroup(array $group, FieldsetInterface $fieldset)
    {
        foreach ($fieldset->getElements() as $fieldsetOrElement) {
            $name = $fieldsetOrElement->getName();
            if (!in_array($name, $group)) {
                $fieldset->remove($name);
            }
        }

        foreach ($fieldset->getFieldsets() as $fieldsetOrElement) {
            $name = $fieldsetOrElement->getName();
            if (!in_array($name, $group) && !array_key_exists($name, $group)) {
                $fieldset->remove($name);
            } elseif (!empty($group[$name])) {
                $this->removeFieldsetElementGroup($group[$name], $fieldsetOrElement);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setData($data)
    {
        if ($data instanceof \Traversable) {
            $data = ArrayUtils::iteratorToArray($data);
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }

        $data = self::filterFormData($this, $data);
        return parent::setData($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareBindData(array $values, array $match)
    {
        $data = [];

        foreach ($values as $name => $value) {
            if (!array_key_exists($name, $match)) {
                if (is_array($value)) {
                    if (!($value = array_filter($value))) {
                        $data[$name] = [];
                    }
                }

                continue;
            }

            if (is_array($value) && is_array($match[$name])) {
                $data[$name] = $this->prepareBindData($value, $match[$name]);
            } else {
                $data[$name] = $value;
            }
        }

        return $data;
    }

    /**
     * @static
     * @param ElementInterface $element
     * @param array|\ArrayAccess|\Traversable $data
     * @return null|array|\ArrayAccess|\Traversable
     */
    protected static function filterFormData(ElementInterface $element, $data)
    {
        if ($element instanceof Collection && $element->getOption('count') == 0) {
            return $data ?: [];
        }

        if (is_array($data)) {
            if ($element instanceof Collection) {
                // Collections are to be recursed
                foreach ($data as $key => $value) {
                    $data[$key] = self::filterFormData($element->getTargetElement(), $value);
                }
            } elseif ($element instanceof FieldsetInterface) {
                // Fieldsets are to be recursed
                foreach ($data as $key => $value) {
                    if ($element->has($key)) {
                        $data[$key] = self::filterFormData($element->get($key), $value);
                    } else {
                        unset($data[$key]);
                    }
                }
            } else {
                // Array for a normal element, make sure there is ANY data in the array
                if (count(array_filter($data)) > 0) {
                    return $data;
                } else {
                    return $data; // null;
                }
            }
        }

        return $data;
    }

    /**
     * @param bool $flag
     * @return self
     */
    private function setUseCaptcha($flag)
    {
        if ($flag) {
            $this->setupCaptchaElement();
        } elseif (!$flag && $this->has('captcha')) {
            /* @var $element \Zend\Form\Element\Captcha */
            $element = $this->get('captcha');
            if (!$this->getCaptchaOptions() && $element->getCaptcha()) {
                $this->setCapcthaOptions($element->getCaptcha());
            }

            $this->remove('captcha');
        }

        $this->useCaptcha = (bool) $flag;
        return $this;
    }

    /**
     * Setup CAPTCHA protection
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
                    'autocomplete' => 'off',
                    'required' => true,
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
     * @param array|\Traversable|AdapterInterface $options
     * @return self
     */
    private function setCapcthaOptions($options)
    {
        if ($options && $this->has('captcha')) {
            /* @var $element \Zend\Form\Element\Captcha */
            $element = $this->get('captcha');
            $element->setCaptcha($options);
        }

        $this->captchaOptions = $options;
        return $this;
    }
    
    /**
     * @return array|\Traversable|AdapterInterface
     */
    private function getCaptchaOptions()
    {
        if (!$this->captchaOptions) {
            $elements = $this->getFormFactory()->getFormElementManager();
            $services = $elements->getServiceLocator();
            if ($services && $services->has('Zend\\Captcha\\AdapterInterface')) {
                $this->setCaptchaOptions($services->get('Zend\\Captcha\\AdapterInterface'));
            }
        }

        return $this->captchaOptions;
    }

    /**
     * @return string
     */
    private function getCaptchaElementName()
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
     * @param int $ttl
     * @return self
     */
    private function setFormTimeout($ttl)
    {
        if ($this->has('csrf')) {
            /* @var $element \Zend\Form\Element\Csrf */
            $element = $this->get('csrf');
            $element->getCsrfValidator()->setTimeout($ttl);
        }

        $this->formTimeout = $ttl;
        return $this;
    }

    /**
     * @param bool $flag
     * @return self
     */
    private function setUseCsrf($flag)
    {
        if ($flag) {
            $this->setupCsrfElement();
        } elseif (!$flag && $this->has('csrf')) {
            /* @var $element \Zend\Form\Element\Csrf */
            $element = $this->get('csrf');
            $this->setFormTimeout($element->getCsrfValidator()->getTimeout());
            $this->remove('csrf');
        }

        $this->useCsrf = (bool) $flag;
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
    private function getCsrfElementName()
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
    private function setUseSubmitElement($flag)
    {
        if ($flag) {
            $this->setupSubmitElement();
        } elseif($this->has('submit')) {
            $this->remove('submit');
        }

        $this->useSubmitElement = (bool) $flag;
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
    private function setUseResetElement($flag)
    {
        if ($flag) {
            $this->setupResetElement();
        } elseif($this->has('reset')) {
            $this->remove('reset');
        }

        $this->useResetElement = (bool) $flag;
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
                ->attach(new SeparatorToSeparator('\\', ''))
                ->attachByName('Word\CamelCaseToUnderscore')
                ->attachByName('Word\DashToUnderscore');
        }

        return $this->nameFilterChain;
    }
}
