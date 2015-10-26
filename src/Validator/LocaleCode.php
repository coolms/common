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

use Locale,
    Zend\Validator\AbstractValidator,
    CmsCommon\Stdlib\LocaleUtils;

/**
 * Locale Validator
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class LocaleCode extends AbstractValidator
{
    /**
     * Error: Not String
     */
    const INVALID = 'notString';

    /**
     * Error: Locale does not exist, or is denied by locale list config
     */
    const NOT_FOUND = 'notFound';

    /**
     * Configured List of allowed locales
     *
     * @var array
     */
    protected $localeList;

    /**
     * Error Message Templates
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid type given. String expected",
        self::NOT_FOUND => "The locale provided does not match any known or allowed locales",
    );

    /**
     * Whether the value is valid
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        if ($value instanceof LocaleCode) {
            $value = LocaleCode::canonicalize($value);
        }

        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        if (!in_array($value, $this->getLocaleList(), true)) {
            $this->error(self::NOT_FOUND);
        }

        return count($this->getMessages()) === 0;
    }

    /**
     * Set locale list to check allowed locales against
     *
     * @param  array $list
     * @return self
     */
    public function setLocaleList(array $list)
    {
        $this->localeList = $list;
        return $this;
    }

    /**
     * Return the locale list for checking allowed locales
     *
     * Lazy loads one if none set
     *
     * @return array
     */
    public function getLocaleList()
    {
        if (!$this->localeList) {
            $this->setLocaleList(LocaleUtils::getList());
        }

        return $this->localeList;
    }
}
