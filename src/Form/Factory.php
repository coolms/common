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

use Zend\Form\Factory as FormFactory,
    CmsCommon\InputFilter\Factory as InputFilterFactory;

class Factory extends FormFactory
{
    /**
     * {@inheritDoc}
     */
    public function getInputFilterFactory()
    {
        if (null === $this->inputFilterFactory) {
            $this->setInputFilterFactory(new InputFilterFactory());
        }

        return $this->inputFilterFactory;
    }
}
