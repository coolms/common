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
    Zend\Form\Exception,
    Zend\Form\FieldsetInterface,
    Zend\Form\Form as ZendForm,
    Zend\InputFilter\CollectionInputFilter,
    Zend\InputFilter\InputFilterAwareInterface,
    Zend\InputFilter\InputFilterInterface,
    Zend\InputFilter\InputFilterProviderInterface,
    Zend\InputFilter\InputProviderInterface,
    Zend\InputFilter\ReplaceableInputInterface,
    Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorAwareTrait,
    Zend\Stdlib\ArrayUtils,
    Zend\Stdlib\PriorityList,
    CmsCommon\Form\Element\StaticElement,
    CmsCommon\Form\Options\Traits\FormOptionsTrait;

class Form extends ZendForm implements
        FormInterface,
        EventManagerAwareInterface,
        ServiceLocatorAwareInterface
{
    use EventManagerAwareTrait,
        FactoryTrait,
        MessagesTrait,
        ServiceLocatorAwareTrait,
        FormOptionsTrait {
            FormOptionsTrait::getUseFormLabel as private;
            FormOptionsTrait::setUseFormLabel as private;
            FormOptionsTrait::getFormTimeout as private;
            FormOptionsTrait::getUseCsrf as private;
            FormOptionsTrait::getUseCaptcha as private;
            FormOptionsTrait::getUseSubmitElement as private;
            FormOptionsTrait::getUseResetElement as private;
        }

    /**
     * @var bool
     */
    protected $hasData = false;

    /**
     * @var bool
     */
    protected $mergeInputFilter = false;

    /**
     * @var bool
     */
    protected $hasMergedInputFilter = false;

    /**
     * @var InputFilterInterface
     */
    protected $inputFilterPrototype;

    /**
     * @var InputFilterInterface
     */
    protected $objectInputFilter;

    /**
     * @var array
     */
    protected $elementGroup = [];

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
    private $nameFilter;

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

        if (isset($options['merge_input_filter'])) {
            $this->setMergeInputFilter($options['merge_input_filter']);
        }

        if (isset($options['element_group'])) {
            $this->setElementGroup($options['element_group']);
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
            case 'merge_input_filter':
                $this->setMergeInputFilter($value);
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
     * @throws Exception\InvalidArgumentException
     * @return self
     */
    public function setElementGroup()
    {
        $argc = func_num_args();
        if (0 === $argc) {
            throw new Exception\InvalidArgumentException(sprintf(
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

        $this->elementGroup = $group;

        return $this;
    }

    /**
     * @return array
     */
    public function getElementGroup()
    {
        return $this->elementGroup;
    }

    /**
     * @param array $group
     * @param FieldsetInterface $fieldset
     * @return self
     */
    private function removeFieldsetElementGroup(array $group, FieldsetInterface $fieldset)
    {
        $elements = ArrayUtils::iteratorToArray($fieldset, false);
        foreach ($elements as $name => $fieldsetOrElement) {
            if ($fieldsetOrElement instanceof Collection) {
                if (!empty($group[$name]) && $fieldsetOrElement->shouldCreateTemplate()) {
                    $this->removeFieldsetElementGroup($group[$name], $fieldsetOrElement->getTemplateElement());
                }

                $fieldsetOrElement = $fieldsetOrElement->getTargetElement();
            }

            if (!empty($group[$name]) && $fieldsetOrElement instanceof FieldsetInterface) {
                $this->removeFieldsetElementGroup($group[$name], $fieldsetOrElement);
                continue;
            }

            if (!in_array($name, $group) && !array_key_exists($name, $group)) {
                $fieldset->remove($name);
            }
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function applyElementGroup()
    {
        if ($group = $this->getElementGroup()) {
            $this->removeFieldsetElementGroup($group, $this)
                ->setValidationGroup($group);
        }

        return $this;
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
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($data) ? get_class($data) : gettype($data))
            ));
        }

        $this->hasData = true;

        $data = self::filterFormData($this, $data);
        return parent::setData($data);
    }

    /**
     * {@inheritDoc}
     */
    public function hasData()
    {
        return $this->hasData;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        $this->applyElementGroup();

        return parent::prepare();
    }

    /**
     * {@inheritDoc}
     */
    protected function prepareBindData(array $values, array $match)
    {
        $data = [];

        foreach ($values as $name => $value) {
            if (!array_key_exists($name, $match)) {
                if (is_array($value) && !($value = array_filter($value))) {
                    $data[$name] = [];
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
        if (!$data && $element instanceof Collection && $element->getOption('count') == 0) {
            return null;
        }

        if (is_array($data)) {
            if ($element instanceof Collection) {
                // Collections are to be recursed
                foreach ($data as $key => $value) {
                    $data[$key] = static::filterFormData($element->getTargetElement(), $value);
                }
            } elseif ($element instanceof FieldsetInterface) {
                // Fieldsets are to be recursed
                foreach ($data as $key => $value) {
                    if ($element->has($key)) {
                        $data[$key] = static::filterFormData($element->get($key), $value);
                    } else {
                        unset($data[$key]);
                    }
                }
            } else {
                // Array for a normal element, make sure there is ANY data in the array
                if (count(array_filter($data)) > 0) {
                    return $data;
                } else {
                    return null; // null?;
                }
            }
        }

        return $data;
    }

    /**
     * @param bool $flag
     * @return self
     */
    public function setMergeInputFilter($flag)
    {
        $this->mergeInputFilter = (bool) $flag;
        return $this;
    }

    /**
     * @return bool
     */
    protected function mergeInputFilter()
    {
        return $this->mergeInputFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function setObject($object)
    {
        $this->objectInputFilter = null;
        $this->hasAddedInputFilterDefaults = false;
        $this->hasMergedInputFilter = false;

        return parent::setObject($object);
    }

    /**
     * @return null|InputFilterInterface
     */
    protected function getObjectInputFilter()
    {
        if (!$this->objectInputFilter) {
            if ($this->object instanceof InputFilterAwareInterface) {
                $this->objectInputFilter = $this->object->getInputFilter();
            } elseif ($this->object instanceof InputFilterProviderInterface) {
                $inputFactory = $this->getFormFactory()->getInputFilterFactory();
                $this->objectInputFilter = $inputFactory->createInputFilter($this->object);
            }
        }

        return $this->objectInputFilter;
    }

    /**
     * {@inheritDoc}
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilterPrototype = $inputFilter;
        $this->hasMergedInputFilter = false;

        return parent::setInputFilter($inputFilter);
    }

    /**
     * {@inheritDoc}
     */
    public function getInputFilter()
    {
        $inputFactory = $this->getFormFactory()->getInputFilterFactory();

        if ($filter = $this->getObjectInputFilter()) {
            if ($this->baseFieldset) {
                if (!$this->filter) {
                    $this->filter = $inputFactory->createInputFilter([]);
                }

                $name = $this->baseFieldset->getName();
                if (!$this->filter->has($name)) {
                    $this->filter->add($filter, $name);
                } elseif (!$this->hasMergedInputFilter && $this->mergeInputFilter()) {
                    if ($this->getPreferFormInputFilter()) {
                        $this->filter = clone $this->inputFilterPrototype;
                        $this->filter->get($name)->merge($filter, true);
                    } else {
                        $filter = clone $filter;
                        $filter->merge($this->filter->get($name), true);
                    }

                    $this->hasMergedInputFilter = true;
                }

                if ($this->filter instanceof ReplaceableInputInterface &&
                    !$this->getPreferFormInputFilter() ||
                    $this->hasMergedInputFilter
                ) {
                    $this->filter->replace($filter, $name);
                }
            } else {
                if (!$this->hasMergedInputFilter && $this->mergeInputFilter()) {
                    if ($this->getPreferFormInputFilter()) {
                        $this->filter = clone $this->inputFilterPrototype;
                        $this->filter->merge($filter, true);
                    } else {
                        $filter = clone $filter;
                        $filter->merge($this->filter, true);
                    }
    
                    $this->hasMergedInputFilter = true;
                }

                if (!$this->filter || !$this->getPreferFormInputFilter()) {
                    $this->filter = $filter;
                }
            }
        }

        if (!$this->filter) {
            $this->filter = $inputFactory->createInputFilter([]);
        }

        if (!$this->hasAddedInputFilterDefaults &&
            $this->filter instanceof InputFilterInterface &&
            $this->useInputFilterDefaults()
        ) {
            $this->attachInputFilterDefaults($this->filter, $this);
            $this->hasAddedInputFilterDefaults = true;
        }

        return $this->filter;
    }

    /**
     * {@inheritDoc}
     */
    public function attachInputFilterDefaults(InputFilterInterface $inputFilter, FieldsetInterface $fieldset)
    {
        $inputFactory = $this->getFormFactory()->getInputFilterFactory();

        if ($fieldset instanceof Collection && $fieldset->getTargetElement() instanceof FieldsetInterface) {
            $elements = $fieldset->getTargetElement()->getElements();
        } else {
            $elements = $fieldset->getElements();
        }

        if (!$fieldset instanceof Collection ||
            !$fieldset->getTargetElement() instanceof FieldsetInterface ||
            $inputFilter instanceof CollectionInputFilter
        ) {
            foreach ($elements as $name => $element) {
                if ($element instanceof StaticElement) {
                    if ($inputFilter->has($name)) {
                        $inputFilter->remove($name);
                    }

                    continue;
                }

                if ($this->getPreferFormInputFilter() && !$this->mergeInputFilter() && $inputFilter->has($name)) {
                    continue;
                }

                if (!$element instanceof InputProviderInterface) {
                    if ($inputFilter->has($name)) {
                        continue;
                    }
                    // Create a new empty default input for this element
                    $input = $inputFactory->createInput(['name' => $name, 'required' => false]);
                } else {
                    // Create an input based on the specification returned from the element
                    $spec  = $element->getInputSpecification();
                    $input = $inputFactory->createInput($spec);

                    if ($inputFilter->has($name) && $inputFilter instanceof ReplaceableInputInterface) {
                        $input->merge($inputFilter->get($name));
                        $inputFilter->replace($input, $name);
                        continue;
                    }
                }

                // Add element input filter to CollectionInputFilter
                if ($inputFilter instanceof CollectionInputFilter && !$inputFilter->getInputFilter()->has($name)) {
                    $inputFilter->getInputFilter()->add($input, $name);
                } else {
                    $inputFilter->add($input, $name);
                }
            }

            if ($fieldset === $this && $fieldset instanceof InputFilterProviderInterface) {
                foreach ($fieldset->getInputFilterSpecification() as $name => $spec) {
                    $input = $inputFactory->createInput($spec);
                    $inputFilter->add($input, $name);
                }
            }
        }

        foreach ($fieldset->getFieldsets() as $name => $childFieldset) {
            if (!$childFieldset instanceof InputFilterProviderInterface) {
                if (!$inputFilter->has($name)) {
                    // Add a new empty input filter if it does not exist (or the fieldset's object input filter),
                    // so that elements of nested fieldsets can be recursively added
                    if ($childFieldset->getObject() instanceof InputFilterAwareInterface) {
                        $inputFilter->add($childFieldset->getObject()->getInputFilter(), $name);
                    // Add input filter for collections via getInputFilterSpecification()
                    } elseif ($childFieldset instanceof Collection
                        && $childFieldset->getTargetElement() instanceof InputFilterProviderInterface
                        && ($spec = $childFieldset->getTargetElement()->getInputFilterSpecification())
                    ) {
                        $collectionContainerFilter = new CollectionInputFilter();
                        $filter = $inputFactory->createInputFilter($spec);

                        // Add child elements from target element
                        $childFieldset = $childFieldset->getTargetElement();

                        foreach ($childFieldset->getElements() as $element) {
                            if ($element instanceof StaticElement) {
                                $filter->remove($element->getName());
                            }
                        }

                        $collectionContainerFilter->setInputFilter($filter);

                        $inputFilter->add($collectionContainerFilter, $name);

                        // We need to copy the inputs to the collection input filter
                        if ($inputFilter instanceof CollectionInputFilter) {
                            $inputFilter = $this->addInputsToCollectionInputFilter($inputFilter);
                        }

                    } else {
                        $inputFilter->add($inputFactory->createInputFilter([]), $name);
                    }
                }

                $fieldsetFilter = $inputFilter->get($name);

                if (!$fieldsetFilter instanceof InputFilterInterface) {
                    // Input attached for fieldset, not input filter; nothing more to do.
                    continue;
                }

                if ($fieldsetFilter instanceof CollectionInputFilter) {
                    $fieldsetFilter = $fieldsetFilter->getInputFilter();
                }

                // Traverse the elements of the fieldset, and attach any
                // defaults to the fieldset's input filter
                $this->attachInputFilterDefaults($fieldsetFilter, $childFieldset);
                continue;
            }

            if (!$this->mergeInputFilter() && $inputFilter->has($name)) {
                // if we already have an input/filter by this name, use it
                continue;
            }

            // Create an input filter based on the specification returned from the fieldset
            $spec   = $childFieldset->getInputFilterSpecification();
            $filter = $inputFactory->createInputFilter($spec);
            if ($inputFilter->has($name)) {
                $filter = $inputFilter->get($name)->merge($filter);
            } else {
                $inputFilter->add($filter, $name);
            }

            // Recursively attach sub filters
            $this->attachInputFilterDefaults($filter, $childFieldset);

            // We need to copy the inputs to the collection input filter to ensure that all sub filters are added
            if ($inputFilter instanceof CollectionInputFilter) {
                $inputFilter = $this->addInputsToCollectionInputFilter($inputFilter);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    private function addInputsToCollectionInputFilter(CollectionInputFilter $inputFilter)
    {
        foreach ($inputFilter->getInputs() as $name => $input) {
            if (!$inputFilter->getInputFilter()->has($name)) {
                $inputFilter->getInputFilter()->add($input, $name);
            }
        }

        return $inputFilter;
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
            if ($services && $services->has(AdapterInterface::class)) {
                $this->setCaptchaOptions($services->get(AdapterInterface::class));
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
                $this->captchaElementName  = $this->getNameFilter()->filter($name);
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
                $this->csrfElementName  = $this->getNameFilter()->filter($name);
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
    private function getNameFilter()
    {
        if (null === $this->nameFilter) {
            $this->nameFilter = (new FilterChain)
                ->attach(new SeparatorToSeparator('\\', ''))
                ->attachByName('Word\CamelCaseToUnderscore')
                ->attachByName('Word\DashToUnderscore');
        }

        return $this->nameFilter;
    }
}
