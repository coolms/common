<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\InputFilter;

use Traversable;

/**
 * @author  Dmitry Popov <d.popov@altgraphic.com>
 */
trait InputFilterProviderTrait
{
    /**
     * Holds the specification which will be returned by getInputFilterSpecification
     *
     * @var array|Traversable
     */
    protected $filterSpec = [];

    /**
     * @return array|Traversable
     */
    public function getInputFilterSpecification()
    {
        return $this->filterSpec;
    }

    /**
     * @param array|Traversable $filterSpec
     */
    public function setInputFilterSpecification($filterSpec)
    {
        $this->filterSpec = $filterSpec;
    }
}
