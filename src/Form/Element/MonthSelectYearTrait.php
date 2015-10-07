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

trait MonthSelectYearTrait
{
    /**
     * @param  int $minYear
     * @return self
     */
    public function setMinYear($minYear)
    {
        $this->minYear = $this->normalizeYear($minYear);
        return $this;
    }

    /**
     * @param  int $maxYear
     * @return self
     */
    public function setMaxYear($maxYear)
    {
        $this->maxYear = $this->normalizeYear($maxYear);
        return $this;
    }

    /**
     * @param string|int $year
     * @return int
     */
    private function normalizeYear($year)
    {
        if (is_int($year) || is_numeric($year)) {
            return $year;
        }

        return (int) (new \DateTime($year))->format('Y');
    }
}
