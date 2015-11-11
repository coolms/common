<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Persistence\Filter;

use Zend\Stdlib\ArraySerializableInterface;

interface FilterInterface extends ArraySerializableInterface
{
    const ANDX                   = 'andX';
    const ORX                    = 'orX';

    const EQUAL                  = 'equal';
    const NOT_EQUAL              = 'notEqual';
    const LESS_THAN              = 'lessThan';
    const LESS_THAN_OR_EQUAL     = 'lessThanOrEqual';
    const GREATER_THAN           = 'greaterThan';
    const GREATER_THAN_OR_EQUAL  = 'greaterThanOrEqual';
    const IN                     = 'in';
    const NOT_IN                 = 'notIn';
    const NOT                    = 'not';
    const NULL                   = 'isNull';
    const NOT_NULL               = 'isNotNull';

    const BEGIN_WITH             = 'beginWith';
    const NOT_BEGIN_WITH         = 'notBeginWith';
    const END_WITH               = 'endWith';
    const NOT_END_WITH           = 'notEndWith';
    const CONTAIN                = 'contain';
    const NOT_CONTAIN            = 'notContain';
    const INSTANCE_OF            = 'instanceOfX';

    public function andX();

    public function orX();

    public function equal($field, $value);

    public function notEqual($field, $value);

    public function lessThan($field, $value);

    public function lessThanOrEqual($field, $value);

    public function greaterThan($field, $value);

    public function greaterThanOrEqual($field, $value);

    public function in($field, $value);

    public function notIn($field, $value);

    public function not();

    public function isNull($field);

    public function isNotNull($field);

    public function beginWith($field, $value);

    public function notBeginWith($field, $value);

    public function endWith($field, $value);

    public function notEndWith($field, $value);

    public function contain($field, $value);

    public function notContain($field, $value);

    public function instanceOfX($field, $value);
}
