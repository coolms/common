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

use Traversable,
    Zend\Form\Element,
    Zend\Form\ElementInterface,
    Zend\Form\Exception,
    Zend\Form\FieldsetInterface,
    Zend\InputFilter\InputFilterProviderInterface,
    CmsCommon\InputFilter\InputFilterProviderTrait;

class InputFilterProviderFieldset extends Fieldset implements InputFilterProviderInterface
{
    use InputFilterProviderTrait;

    /**
     * Set options for a fieldset. Accepted options are:
     * - input_filter_spec: specification to be returned by getInputFilterSpecification
     *
     * @param  array|Traversable $options
     * @return Element|ElementInterface|FieldsetInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['input_filter_spec'])) {
            $this->setInputFilterSpecification($options['input_filter_spec']);
        }

        return $this;
    }
}
