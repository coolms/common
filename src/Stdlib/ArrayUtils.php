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

use Zend\Stdlib\ArrayUtils as BaseArrayUtils,
    Zend\Stdlib\Exception;

/**
 * Utility class for testing and manipulation of PHP arrays.
 *
 * Declared abstract, as we have no need for instantiation.
 */
abstract class ArrayUtils extends BaseArrayUtils
{
    /**
     * Exactly the same as array_filter except this function
     * filters within multi-dimensional arrays
     *
     * @param array $array
     * @param callable $callback optional callback function name
     * @param bool $removeEmptyArrays optional flag removal of empty arrays after filtering
     * @return array
     */
    public static function filterRecursive(array $array, $callback = null, $removeEmptyArrays = false)
    {
        if (null !== $callback && !is_callable($callback)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Second parameter of %s must be callable',
                __METHOD__
            ));
        }

        foreach ($array as $key => &$value) { // mind the reference
            if (is_array($value)) {
                $value = static::filterRecursive($value, $callback, $removeEmptyArrays);

                if ($removeEmptyArrays && ! (bool) $value) {
                    unset($array[$key]);
                }
            } else {
                if (null !== $callback && !$callback($value)) {
                    unset($array[$key]);
                } elseif (! (bool) $value) {
                    unset($array[$key]);
                }
            }
        }

        unset($value); // kill the reference

        return $array;
    }
}
