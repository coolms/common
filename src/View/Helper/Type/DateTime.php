<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\View\Helper\Type;

use DateTime as DateTimeObject,
    IntlDateFormatter,
    Zend\I18n\View\Helper\DateFormat;

class DateTime extends AbstractHelper
{
    /**
     * @var string
     */
    protected $instanceType = DateTimeObject::class;

    /**
     * @var DateFormat
     */
    protected $dateFormatHelper;

    /**
     * @var string
     */
    protected $defaultDateFormatHelper = 'dateFormat';

    /**
     * {@inheritDoc}
     */
    protected function format($value, $dateFormat = IntlDateFormatter::GREGORIAN, $timeFormat = IntlDateFormatter::MEDIUM)
    {
        $dateFormatHelper = $this->getDateFormatHelper();
        return $dateFormatHelper($value, $dateFormat, $timeFormat);
    }

    /**
     * @return DateFormat
     */
    protected function getDateFormatHelper()
    {
        if (null === $this->dateFormatHelper) {
            if (method_exists($this->view, 'plugin')) {
                $this->dateFormatHelper = $this->view->plugin($this->defaultDateFormatHelper);
            }

            if (!$this->dateFormatHelper instanceof DateFormat) {
                $this->dateFormatHelper = new DateFormat();
                $this->dateFormatHelper->setView($this->getView());
            }
        }

        return $this->dateFormatHelper;
    }
}
