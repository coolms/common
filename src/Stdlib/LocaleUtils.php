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

use Locale,
    ResourceBundle;

/**
 * Utility class for testing and manipulation of available locales.
 *
 * Declared abstract, as we have no need for instantiation.
 */
abstract class LocaleUtils
{
    /**
     * @return array
     */
    public static function getList($sortFlags = SORT_LOCALE_STRING)
    {
        $locales = ResourceBundle::getLocales('');

        sort($locales);

        return $locales;
    }

    /**
     * @return array
     */
    public static function getNamedList($locale = null, $sortFlags = SORT_LOCALE_STRING)
    {
        $locales = [];
        foreach (static::getList() as $code) {
            $locales[$code] = Locale::getDisplayName($code, $locale);
        }

        asort($locales, $sortFlags);

        return $locales;
    }
}
