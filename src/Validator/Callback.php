<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Validator;

use Zend\Validator\Callback as ZendCallbackValidator;

class Callback extends ZendCallbackValidator
{
    /**
     * {@inheritDoc}
     */
    public function setOptions($options = [])
    {
        if (isset($options['messageVariables'])) {
            $this->abstractOptions['messageVariables'] = $options['messageVariables'];
        }

        return parent::setOptions($options);
    }
}
