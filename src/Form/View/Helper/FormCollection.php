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

use Zend\Form\Element\Collection,
    Zend\Form\ElementInterface,
    Zend\Form\FieldsetInterface,
    Zend\Form\LabelAwareInterface,
    Zend\Form\View\Helper\FormCollection as ZendFormCollection,
    Zend\Stdlib\ArrayUtils;
use Zend\I18n\Translator\TranslatorAwareInterface;
use CmsCommon\Form\View\Helper\Traits\TranslatorTextDomainTrait;

/**
 * Helper for rendering a collection
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class FormCollection extends ZendFormCollection
{
    use FormProviderTrait;

    /**
     * @var string
     */
    protected $wrapper = "<fieldset%4\$s>\n%2\$s\n%1\$s%3\$s\n</fieldset>";

    /**
     * @var string
     */
    protected $fieldsetKey = 'fieldset';

    /**
     * @var integer
     */
    protected $partialCounter;

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
     * @param  ElementInterface $element     Form collection or fieldset to populate in the view
     * @param  bool $wrap           
     * @param  string|bool $partial Name of partial view script
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function __invoke(ElementInterface $element = null, $wrap = true, $partial = true)
    {
        if (!$element) {
            return $this;
        }

        if ($element instanceof Collection
            && ($element->allowRemove() || $element->allowAdd())
        ) {
            $headScript = $this->getView()->plugin('headScript');
            $basePath   = $this->getView()->plugin('basePath');
            $headScript()->appendFile($basePath('assets/common/js/form/collection.js'));
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

        $wrap = $this->shouldWrap();
        $markup = '';
        $templateMarkup = '';

        if (($partial = $this->getPartial()) === true) {
            $partial = $element->getOption('partial');
        }

        if ($partial) {
            if ($element instanceof Collection) {
                $markup = $this->renderCollection($element, false, $partial);
            } else {
                $markup = $this->getView()->render($partial, [
                    $this->getFormKey()     => $this->getForm(),
                    $this->getFieldsetKey() => $element,
                    'legend'                => $this->renderLegend($element),
                    'allowAdd'              => false,
                    'allowRemove'           => false,
                    'counter'               => null,
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
                    		$this->renderLegend($templateElement)
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
            $legend = $this->renderLegend($element); //$this->translateAndRender($element, 'renderLegend');
        	$markup = $this->wrap($element, $markup, $legend, $templateMarkup);
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
        $markup         = '';
        $elementHelper  = $this->getElementHelper();

        // reset the counter if it's called again
        $this->partialCounter = 0;
        foreach ($fieldset as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                if ($fieldset instanceof Collection) {
                    if ($this->partialCounter >= $fieldset->getOption('count')) {
                    	$elementOrFieldset->setOption('allow_remove', $fieldset->allowRemove());
                    } else {
                    	$elementOrFieldset->setOption('allow_remove', false);
                    }
                    $this->partialCounter++;
                }

                $markup .= $this($elementOrFieldset);

            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $elementHelper($elementOrFieldset);
            }
        }

        return $markup;
    }

    /**
     * @param Collection $collection
     * @param bool $wrap                Should 
     * @param string $partial           Name of the partial view script
     * @return string
     */
    protected function renderCollection(Collection $collection, $wrap, $partial)
    {
        if ($collection instanceof \Traversable) {
            $fieldsets = ArrayUtils::iteratorToArray($collection, false);
        } elseif (is_object($collection) && method_exists($collection, 'toArray')) {
            $fieldsets = $collection->toArray();
        }

        $renderer    = $this->getView();
        $markup      = $this->renderHiddenElement($collection);
        $fieldsetKey = $this->getFieldsetKey();

        $vars['form'] = $this->getForm();
        $vars['collection'] = $collection;

        // reset the counter if it's called again
        $this->partialCounter = 0;
        foreach ($fieldsets as $fieldset) {
            if ($fieldsetKey) {
                $fieldset->setAttribute('data-counter', $this->partialCounter);

                if ($this->partialCounter >= $collection->getOption('count')) {
                	$vars['allowRemove'] = $collection->allowRemove();
                	$fieldset->setOption('allow_remove', $collection->allowRemove());
                } else {
                	$vars['allowRemove'] = false;
                	$fieldset->setOption('allow_remove', false);
                }

                $vars[$fieldsetKey] = $fieldset;
                $vars['allowAdd']   = $collection->allowAdd();
                $vars['counter']    = $this->partialCounter;
                $vars['legend']     = $this->renderLegend($fieldset);

            } else {
                $vars = $fieldset;
            }

            $fieldsetMarkup = $renderer->render($partial, $vars);
            if ($wrap) {
                $markup .= $this->wrap(
                    $fieldset,
                    $fieldsetMarkup,
                    isset($vars['legend']) ? $vars['legend'] : $this->renderLegend($fieldset)
                );
            } else {
                $markup .= $fieldsetMarkup;
            }

            $this->partialCounter++;
        }

        if ($fieldsetKey && $collection->shouldCreateTemplate()) {

            $fieldset = $collection->getTemplateElement();

            $templatePlaceholder = $collection->getTemplatePlaceholder();

            $fieldset->setOption('allow_remove', $collection->allowRemove());
            $fieldset->setAttribute('data-counter', $templatePlaceholder);

            $vars[$fieldsetKey]     = $fieldset;
            $vars['counter']        = $templatePlaceholder;
            $vars['allowRemove']    = $collection->allowRemove();
            $vars['legend']         = $this->renderLegend($fieldset);

            $templateMarkup = $renderer->render($partial, $vars);

            if ($wrap) {
                $templateMarkup = $this->wrap(
                    $fieldset,
                    $templateMarkup,
                    $vars['legend']
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
        if (!$element instanceof LabelAwareInterface
            || !$element->getLabelOption('disable_html_escape')
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
    protected function getControl(ElementInterface $element)
    {
        $control = '';

        if ($element instanceof Collection && $element->allowAdd()) {
            $control = ' <button type="button" class="btn btn-success btn-xs" '
                     . 'onclick="return CmsCommon.Form.Collection.addFieldset(this, \'prepend\');">'
                     . '+</button>';
        } elseif ($element instanceof FieldsetInterface && $element->getOption('allow_remove')) {
            $control = ' <button type="button" class="btn btn-danger btn-xs" '
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
    protected function wrap(ElementInterface $element, $markup, $legend, $templateMarkup = '')
    {
        if ($attributes = $element->getAttributes()) {
            unset($attributes['name']);
        }

        return sprintf(
            $this->getWrapper(),
            $markup,
            $legend,
            $templateMarkup,
            count($attributes) ? ' ' . $this->createAttributesString($attributes) : ''
        );
    }

    protected function translate()
    {
        if (is_array($renderer)) {
            $helper = $renderer[0];
        } else {
            $helper = $renderer;
        }
        
        $textDomain = $element->getOption('text_domain');
        if (!$textDomain || !($helper instanceof TranslatorAwareInterface && $helper->isTranslatorEnabled())) {
            return call_user_func($renderer, $element);
        }
        
        $translator = $helper->getTranslator();
        if (!($isEventManagerEnabled = $translator->isEventManagerEnabled())) {
            $translator->enableEventManager();
        }
        
        $translatorEventManager = $translator->getEventManager();
        $isMissingTranslation   = false;
        $rollbackTextDomain     = $helper->getTranslatorTextDomain();
        
        if ($element instanceof Captcha) {
            $captchaHelper = $this->getView()->plugin($element->getCaptcha()->getHelperName());
            $captchaHelper->setTranslatorTextDomain($rollbackTextDomain);
        } else {
            $captchaHelper = null;
        }
        
        // Element text domain
        if ($textDomain !== $rollbackTextDomain) {
            $callbackHandler = $translatorEventManager->attach(
                Translator::EVENT_MISSING_TRANSLATION,
                function($e) use (&$isMissingTranslation, $helper, $textDomain, $captchaHelper) {
                    $helper->setTranslatorTextDomain($textDomain);
                    if ($captchaHelper) {
                        $captchaHelper->setTranslatorTextDomain($textDomain);
                    }
                    $isMissingTranslation = true;
                });
        }
        
        $markup = call_user_func($renderer, $element);
        if ($isMissingTranslation) {
            $markup = call_user_func($renderer, $element);
        }
        
        // Rollback text doamin for element helper
        $helper->setTranslatorTextDomain($rollbackTextDomain);
        if ($captchaHelper) {
            $captchaHelper->setTranslatorTextDomain($rollbackTextDomain);
        }
        
        if (isset($callbackHandler)) {
            $translatorEventManager->detach($callbackHandler);
        }
        if (!$isEventManagerEnabled) {
            $translator->disableEventManager();
        }
        
        return $markup;
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
     * @return bool|string
     */
    public function getPartial()
    {
        return $this->partial;
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
