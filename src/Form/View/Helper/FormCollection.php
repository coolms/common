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

use ReflectionClass,
    Traversable,
    Zend\Form\Element\Collection,
    Zend\Form\ElementInterface,
    Zend\Form\FieldsetInterface,
    Zend\Form\LabelAwareInterface,
    Zend\Form\View\Helper\FormCollection as FormCollectionHelper,
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\Stdlib\ArrayUtils;

/**
 * Helper for rendering a collection
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class FormCollection extends FormCollectionHelper
{
    use FormProviderTrait;

    /**
     * @var string
     */
    protected $wrapper = "<fieldset%5\$s>\n%2\$s\n%3\$s\n%1\$s%4\$s\n</fieldset>";

    /**
     * @var string
     */
    protected $descriptionWrapper = '<div>%s</div>';

    /**
     * @var string
     */
    protected $fieldsetKey = 'fieldset';

    /**
     * @var bool|string
     */
    protected $partial = true;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->validGlobalAttributes['data-counter'] = true;
    }

    /**
     * Render a collection by iterating through all fieldsets and elements.
     *
     * If no arguments are provided, returns object instance.
     *
     * @param  ElementInterface $element    Form collection or fieldset to populate in the view
     * @param  bool $wrap
     * @param  string|bool $partial         Name of partial view script
     * @return string|self
     */
    public function __invoke(ElementInterface $element = null, $wrap = true, $partial = true)
    {
        if (0 === func_num_args()) {
            return $this;
        }

        if ($element instanceof Collection &&
            ($element->allowRemove() || $element->allowAdd())
        ) {
            $headScript = $this->getView()->plugin('headScript');
            $basePath   = $this->getView()->plugin('basePath');
            $headScript()->appendFile($basePath('assets/cms-common/js/form/collection.js'));
        }

        $this->setShouldWrap($wrap);
        $this->setPartial($partial);

        return $this->render($element);
    }

    /**
     * {@inheritDoc}
     */
    public function render(ElementInterface $element)
    {
        if (!method_exists($this->getView(), 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        return $this->renderFieldset($element);
    }

    /**
     * @param FieldsetInterface $element
     * @return string
     */
    protected function renderFieldset(FieldsetInterface $element)
    {
        $wrap = $this->shouldWrap();
        $markup = '';
        $templateMarkup = '';

        if ($partial = $this->getPartial($element)) {
            if ($element instanceof Collection) {
                $markup = $this->renderCollection($element, false, $partial);
            } else {
                $markup = $this->getView()->render($partial, [
                    $this->getFormKey()     => $this->getForm(),
                    $this->getFieldsetKey() => $element,
                    'legend'                => $this->renderLegend($element),
                    'description'           => $this->renderDescription($element),
                    'allowAdd'              => false,
                    'allowRemove'           => false,
                    'counter'               => $element->getAttribute('data-counter'),
                    'wrap'                  => $wrap,
                ]);

                $wrap = false;
            }
        } else {
            if ($element instanceof Collection) {
                $markup = $this->renderHiddenElement($element);

                if ($element->shouldCreateTemplate()) {

                    $escapeHtmlAttribHelper = $this->getEscapeHtmlAttrHelper();
                    $templateElement        = $element->getTemplateElement();
                    $templateMarkup         = $this->renderElements($templateElement);

                    $templateElement->setOption('allow_remove', $element->allowRemove());
                    $templateElement->setAttribute('data-counter', $element->getTemplatePlaceholder());

                    if ($wrap) {
                    	$templateMarkup = $this->wrap(
                    	    $templateElement,
                    		$templateMarkup,
                    		$this->renderLegend($templateElement),
                    	    $this->renderDescription($templateElement)
                    	);
                    }

                    $templateMarkup = sprintf(
                        $this->getTemplateWrapper(),
                        $escapeHtmlAttribHelper($templateMarkup)
                    );
                }
            }

            $markup .= $this->renderElements($element);
        }

        // Every collection is wrapped by a fieldset if needed
        if ($wrap) {
            $legend = $this->renderLegend($element);
            $description = $this->renderDescription($element);
        	$markup = $this->wrap(
            	    $element,
            	    $markup,
            	    $legend,
            	    $description,
            	    $templateMarkup
        	    );
        } else {
        	$markup .= $templateMarkup;
        }

        return $markup;
    }

    
    /**
     * Render a collection by iterating through all
     * fieldsets and elements
     *
     * @param FieldsetInterface $element
     * @return string
     */
    protected function renderElements(FieldsetInterface $fieldset)
    {
        $markup = '';
        $elementHelper = $this->getElementHelper();

        $elements = ArrayUtils::iteratorToArray($fieldset, false);
        foreach ($elements as $key => $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface &&
                $fieldset instanceof Collection
            ) {
                $elementOrFieldset->setAttribute('data-counter', $key);
            	$elementOrFieldset->setOption(
            	    'allow_remove',
            	    $key >= $fieldset->getOption('count')
            	       ? $fieldset->allowRemove()
            	       : false
            	);
            }

            $markup .= $elementHelper($elementOrFieldset);
        }

        $this->reset($fieldset);

        return $markup;
    }

    /**
     * @param Collection    $collection
     * @param bool          $wrap       
     * @param string        $partial    Name of the partial view script
     * @return string
     */
    protected function renderCollection(Collection $collection, $wrap, $partial)
    {
        if ($collection instanceof Traversable) {
            $fieldsets = ArrayUtils::iteratorToArray($collection, false);
        } elseif (is_object($collection) && method_exists($collection, 'toArray')) {
            $fieldsets = $collection->toArray();
        }

        $renderer    = $this->getView();
        $markup      = $this->renderHiddenElement($collection);
        $fieldsetKey = $this->getFieldsetKey();

        $vars['form']       = $this->getForm();
        $vars['collection'] = $collection;

        foreach ($fieldsets as $key => $fieldset) {
            if ($fieldsetKey) {
                $fieldset->setAttribute('data-counter', $key);

                if ($key >= $collection->getOption('count')) {
                	$vars['allowRemove'] = $collection->allowRemove();
                	$fieldset->setOption('allow_remove', $collection->allowRemove());
                } else {
                	$vars['allowRemove'] = false;
                	$fieldset->setOption('allow_remove', false);
                }

                $vars[$fieldsetKey]  = $fieldset;
                $vars['allowAdd']    = $collection->allowAdd() && $collection->shouldCreateTemplate();
                $vars['counter']     = $key;
                $vars['legend']      = $this->renderLegend($fieldset);
                $vars['description'] = $this->renderDescription($fieldset);

            } else {
                $vars = $fieldset;
            }

            $fieldsetMarkup = $renderer->render($partial, $vars);
            if ($wrap && is_array($vars)) {
                $markup .= $this->wrap(
                        $fieldset,
                        $fieldsetMarkup,
                        $vars['legend'],
                        $vars['description']
                    );
            } else {
                $markup .= $fieldsetMarkup;
            }
        }

        if ($fieldsetKey && $collection->shouldCreateTemplate()) {
            $templatePlaceholder = $collection->getTemplatePlaceholder();

            $fieldset = $collection->getTemplateElement();
            $fieldset->setOption('allow_remove', $collection->allowRemove());
            $fieldset->setAttribute('data-counter', $templatePlaceholder);

            $vars[$fieldsetKey]     = $fieldset;
            $vars['counter']        = $templatePlaceholder;
            $vars['allowRemove']    = $collection->allowRemove();
            $vars['legend']         = $this->renderLegend($fieldset);
            $vars['description']    = $this->renderDescription($fieldset);

            $templateMarkup = $renderer->render($partial, $vars);

            if ($wrap) {
                $templateMarkup = $this->wrap(
                        $fieldset,
                        $templateMarkup,
                        $vars['legend'],
                        $vars['description']
                    );
            }

            $escapeHtmlAttrHelper = $this->getEscapeHtmlAttrHelper();
            $templateMarkup = $escapeHtmlAttrHelper($templateMarkup);

            $markup .= sprintf($this->getTemplateWrapper(), $templateMarkup);
        }

        return $markup;
    }

    /**
     * @param Collection $collection
     * @return string
     */
    protected function renderHiddenElement(Collection $collection)
    {
        if ($collection->getOption('count') == 0) {
        	$hidden = $this->getView()->plugin('formHidden');
        	return $hidden($collection) . "\n";
        }
    }

    /**
     * @param ElementInterface $element
     * @return string
     */
    protected function renderLegend(ElementInterface $element)
    {
        if (!($this->getLabelWrapper() && ($label = $element->getLabel()))) {
            return '';
        }

        if (null !== ($translator = $this->getTranslator())) {
            $label = $translator->translate($label, $this->getTranslatorTextDomain());
        }

        if (!$element instanceof LabelAwareInterface ||
            !$element->getLabelOption('disable_html_escape')
        ) {
            $escapeHtmlHelper = $this->getEscapeHtmlHelper();
            $label = $escapeHtmlHelper($label);
        }

        return sprintf($this->getLabelWrapper(), $label . "\n" . $this->getControl($element));
    }

    /**
     * @param ElementInterface $element
     * @return string
     */
    protected function renderDescription(ElementInterface $element)
    {
        if (!($this->getDescriptionWrapper() &&
            ($description = $element->getOption('description')))
        ) {
            return '';
        }

        if (null !== ($translator = $this->getTranslator())) {
            $description = $translator->translate($description, $this->getTranslatorTextDomain());
        }
 
        if (!$element instanceof LabelAwareInterface ||
            !$element->getLabelOption('disable_html_escape')
        ) {
            $escapeHtmlHelper = $this->getEscapeHtmlHelper();
            $description = $escapeHtmlHelper($description);
        }

        return sprintf($this->getDescriptionWrapper(), $description);
    }

    /**
     * @param ElementInterface $element
     * @return string
     */
    protected function getControl(ElementInterface $element)
    {
        $control = '';

        if ($element instanceof Collection && $element->allowAdd() && $element->shouldCreateTemplate()) {
            $control = ' <button type="button" class="btn btn-add-fieldset" '
                     . 'onclick="return CmsCommon.Form.Collection.addFieldset(this, \'prepend\');">'
                     . '+</button>';
        } elseif ($element instanceof FieldsetInterface
            && !$element instanceof Collection
            && $element->getOption('allow_remove')
        ) {
            $control = ' <button type="button" class="btn btn-remove-fieldset" '
                     . 'onclick="return CmsCommon.Form.Collection.removeFieldset(this);">'
                     . '-</button>';
        }

        return $control;
    }

    /**
     * @param ElementInterface $element
     * @param string $markup
     * @param string $legend
     * @param string $templateMarkup
     * @return string
     */
    protected function wrap(
        ElementInterface $element,
        $markup,
        $legend,
        $description,
        $templateMarkup = ''
    ) {
        if ($attributes = $element->getAttributes()) {
            unset($attributes['name']);
        }

        return sprintf(
            $this->getWrapper(),
            $markup,
            $legend,
            $description,
            $templateMarkup,
            count($attributes) ? ' ' . $this->createAttributesString($attributes) : ''
        );
    }

    /**
     * @param ElementInterface $element
     */
    protected function reset(ElementInterface $element)
    {
        if ($element instanceof FieldsetInterface) {
            foreach ($element as $elementOrFieldset) {
                if ($elementOrFieldset instanceof FieldsetInterface) {
                    $this->reset($elementOrFieldset);
                } else {
                    if ($elementOrFieldset->getOption(FormRow::RENDERED)) {
                        $elementOrFieldset->setOption(FormRow::RENDERED, null);
                    }
                }
            }
        }

        if ($element->getOption(FormRow::RENDERED)) {
            $element->setOption(FormRow::RENDERED, null);
        }
    }

    /**
     * Set the description-wrapper
     * The string will be passed through sprintf with the description as single
     * parameter
     * This defaults to '<div>%s</div>'
     *
     * @param string $descriptionWrapper
     * @return self
     */
    public function setDescriptionWrapper($descriptionWrapper)
    {
        $this->descriptionWrapper = $descriptionWrapper;
        return $this;
    }

    /**
     * Get the wrapper for the description
     *
     * @return string
     */
    public function getDescriptionWrapper()
    {
        return $this->descriptionWrapper;
    }

    /**
     * @param bool|string $partial
     * @return self
     */
    public function setPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPartial(FieldsetInterface $element = null)
    {
        $renderer = $this->getView();

        if ($element &&
            true === $this->partial &&
            method_exists($renderer, 'resolver') &&
            ($partial = $element->getOption('partial'))
        ) {
            $class = new ReflectionClass($renderer);
            if ($class->hasMethod('getTemplate')) {
                $template = $renderer->getTemplate();
            } elseif ($class->hasProperty('__template')) {
                $property = $class->getProperty('__template');
                $property->setAccessible(true);
                $template = $property->getValue($renderer);
            } else {
                return;
            }

            if (is_string($partial) &&
                is_string($template) &&
                realpath($renderer->resolver($partial)) ===
                    realpath($renderer->resolver($template))
            ) {
                return;
            }

            return $partial;
        }

        if (is_string($this->partial)) {
            return $this->partial;
        }
    }

    /**
     * @param string $key
     * @return self
     */
    public function setFieldsetKey($key)
    {
        $this->fieldsetKey = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldsetKey()
    {
        return $this->fieldsetKey;
    }
}
