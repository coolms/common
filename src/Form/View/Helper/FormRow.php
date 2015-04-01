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

use Zend\Form\ElementInterface,
    Zend\Form\LabelAwareInterface,
    Zend\Form\View\Helper\FormRow as ZendFormRow;
use Zend\I18n\Translator\TranslatorAwareInterface;

class FormRow extends ZendFormRow
{
    const RENDER_ALL     = 'all';
    const RENDER_STATIC  = 'static';
    const RENDER_DYNAMIC = 'dynamic';

    /**
     * @var string
     */
    protected $renderMode = self::RENDER_ALL;

    /**
     * {@inheritDoc}
     * 
     * @param string $renderMode
     */
    public function __invoke(ElementInterface $element = null, $labelPosition = null,
            $renderErrors = null, $partial = null, $renderMode = null)
    {
        if (!$element) {
            return $this;
        }

        if (null === $labelPosition && $element instanceof LabelAwareInterface) {
            if (!$element->getLabelOption('position')) {
            	switch ($element->getAttribute('type')) {
            		case 'radio':
            		case 'checkbox':
            			$labelPosition = static::LABEL_APPEND;
            			break;
            		default:
            		    $labelPosition = static::LABEL_PREPEND;
            	}
            }
            $this->setLabelPosition($element->getLabelOption('position') ?: self::LABEL_PREPEND);
        }

        if (null !== $renderMode) {
            $this->setRenderMode($renderMode);
        }

        return parent::__invoke($element, $labelPosition, $renderErrors, $partial);
    }

    /**
     * {@inheritDoc}
     */
    public function render(ElementInterface $element)
    {
        if ($element->getOption('__rendered__')
            || ($element->getAttribute('type') === 'static'
                && ($this->getRenderMode() === static::RENDER_DYNAMIC
                    || null === $element->getValue()))
        ) {
            return '';
        }

        if ($element instanceof LabelAwareInterface) {
    		switch ($element->getAttribute('type')) {
    			case 'radio':
    			case 'checkbox':
    			    if ($element->getLabelOption('always_wrap') !== false) {
    			        $element->setLabelOption('always_wrap', true);
    			    }
    			    if (!$element->getLabelOption('position')) {
    				    $labelPosition = static::LABEL_APPEND;
    			    }
    				break;
    			case 'static':
    			    if ($element->getLabelOption('always_wrap') !== false) {
    			    	$element->setLabelOption('always_wrap', true);
    			    }
    			default:
    			    if (!$element->getLabelOption('position')) {
    				    $labelPosition = static::LABEL_PREPEND;
    			    }
    		}
        	$this->setLabelPosition($element->getLabelOption('position') ?: $labelPosition);
        }

        return parent::render($element);
    }

    /**
     * {@inheritDoc}
     */
    protected function getElementHelper()
    {
        $renderer = $this->getView();
        if ($this->getRenderMode() === static::RENDER_STATIC
            && method_exists($renderer, 'plugin')
        ) {
            $elementHelper = $renderer->plugin('formstatic');
        } else {
            $elementHelper = parent::getElementHelper();
        }

        if ($elementHelper instanceof TranslatorAwareInterface) {
            $elementHelper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        return $elementHelper;
    }

    /**
     * @param string $mode
     * @return self
     */
    public function setRenderMode($mode)
    {
        $this->renderMode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getRenderMode()
    {
        return $this->renderMode;
    }
}
