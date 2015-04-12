<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Stdlib;

use Zend\Stdlib\AbstractOptions;

interface OptionsProviderInterface
{
    /**
     * Retrieves options
     *
     * @return AbstractOptions
     */
    public function getOptions();

    /**
     * Sets options
     *
     * @param AbstractOptions $options
     * @return self
     */
    public function setOptions(AbstractOptions $options);

    /**
     * Checks whether object has options
     *
     * @return bool
     */
    public function hasOptions();
}
