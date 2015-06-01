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

trait OptionsProviderTrait
{
    /**
     * @var AbstractOptions
     */
    protected $options;

    /**
     * Retrieves options
     *
     * @access public
     * @throws Exception\OptionsNotFoundException If options tried to retrieve without being set
     * @return AbstractOptions
     */
    public function getOptions()
    {
        if (!$this->hasOptions()) {
            throw new Exception\OptionsNotFoundException(
                'Options were tried to retrieve but not set'
            );
        }

        return $this->options;
    }

    /**
     * Sets options
     *
     * @access public
     * @param AbstractOptions $options
     * @return self
     */
    public function setOptions(AbstractOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Checks whether object has options
     *
     * @access public
     * @return bool
     */
    public function hasOptions()
    {
        return $this->options instanceof AbstractOptions;
    }
}
