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
    Zend\Form\View\Helper\FormElement as ZendFormElement,
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\I18n\Translator\TranslatorAwareTrait;

class FormElement extends ZendFormElement implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    /**
     * __construct
     */
    public function __construct()
    {
        if (!isset($this->classMap['Zend\Form\Fieldset'])) {
            $this->addClass('Zend\Form\Fieldset', 'formcollection');
        }
        if (!isset($this->typeMap['static'])) {
            $this->addType('static', 'formstatic');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function renderHelper($name, ElementInterface $element)
    {
        $helper = $this->getView()->plugin($name);
        if ($helper instanceof TranslatorAwareInterface) {
            $helper->setTranslatorTextDomain($this->getTranslatorTextDomain());
        }

        return $helper($element);
    }
}
