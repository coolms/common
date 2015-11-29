<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\Element;

use DateTime,
    IntlDateFormatter,
    InvalidArgumentException,
    Locale;

trait DateTimeTrait
{
    /**
     * @var string|Locale
     */
    protected $locale;

    /**
     * @var IntlDateFormatter
     */
    protected $dateFormatter;

    /**
     * @param DateTime $date
     * @return string
     */
    protected function format(DateTime $date)
    {
        return $this->getDateFormatter()->format($date);
    }

    /**
     * Normalize the provided value to a DateTime object
     *
     * @param  string|int|DateTime $value
     * @throws InvalidArgumentException
     * @return DateTime
     */
    protected function normalizeDateTime($value)
    {
        try {
            if (is_int($value)) {
                //timestamp
                $value = new DateTime("@$value");
            } elseif (!$value instanceof DateTime) {
                $value = new DateTime($value);
            }
        } catch (\Exception $e) {
            throw new InvalidArgumentException('Invalid date string provided', $e->getCode(), $e);
        }

        return $value;
    }

    /**
     * @return IntlDateFormatter
     */
    protected function getDateFormatter()
    {
        if (null === $this->dateFormatter) {
            $this->dateFormatter = new IntlDateFormatter(
                $this->getLocale(),
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE
            );
        }

        return $this->dateFormatter;
    }

    /**
     * @param string|Locale $locale
     * @return self
     */
    public function setLocale($locale)
    {
        $this->locale = Locale::canonicalize($locale);
        return $this;
    }

    /**
     * @return string|Locale
     */
    public function getLocale()
    {
        if (null === $this->locale) {
            return Locale::getDefault();
        }

        return $this->locale;
    }
}
