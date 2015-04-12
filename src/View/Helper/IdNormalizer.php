<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper;

use Zend\Filter\FilterChain,
    Zend\Filter\FilterInterface,
    Zend\View\Helper\AbstractHelper;

/**
 * Html element id normalizer view helper
 */
class IdNormalizer extends AbstractHelper
{
    /**
     * @var FilterInterface
     */
    private static $idFilter;

    /**
     * @param string $id
     * @return string
     */
    public function __invoke($id)
    {
        $id = trim(str_replace(['][','[',']'], '-', $id), '-');
        return static::getIdFilter()->filter($id);
    }

    /**
     * @return FilterInterface
     */
    private static function getIdFilter()
    {
        if (null === static::$idFilter) {
            static::$idFilter = new FilterChain([
                'filters' => [
                    ['name' => 'Word\\CamelCaseToDash'],
                    ['name' => 'Word\\UnderscoreToDash'],
                    ['name' => 'StringToLower'],
                ],
            ]);
        }

        return static::$idFilter;
    }
}
