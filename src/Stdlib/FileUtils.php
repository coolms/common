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

use Zend\Form\FieldsetInterface,
    Zend\Form\Element\Collection,
    Zend\Form\Element\File;

/**
 * FileUtils
 *
 * Declared abstract, as we have no need for instantiation.
 */
abstract class FileUtils
{
    /**
     * @param string $from
     * @param string $to
     * @param string $separator
     * @return string
     */
    public static function relativePath($from, $to, $separator = DIRECTORY_SEPARATOR)
    {
        $from = explode($separator, rtrim($from, $separator));
        $to   = explode($separator, rtrim($to, $separator));

        while(count($from) && count($to) && $from[0] === $to[0]) {
            array_shift($from);
            array_shift($to);
        }

        return str_pad('', count($from) * 3, '..' . $separator) . implode($separator, $to);
    }
}
