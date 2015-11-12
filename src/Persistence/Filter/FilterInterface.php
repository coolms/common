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

interface FilterInterface
{
    const ANDX                   = 'andX';
    const ORX                    = 'orX';

    const EQUAL                  = 'equal';
    const NOT_EQUAL              = 'notEqual';
    const GREATER_THAN           = 'greaterThan';
    const GREATER_THAN_OR_EQUAL  = 'greaterThanOrEqual';
    const LESS_THAN              = 'lessThan';
    const LESS_THAN_OR_EQUAL     = 'lessThanOrEqual';
    const IN                     = 'in';
    const NOT_IN                 = 'notIn';
    const NOT                    = 'not';
    const IS_NULL                = 'isNull';
    const IS_NOT_NULL            = 'isNotNull';
    const BETWEEN                = 'between';

    const BEGIN_WITH             = 'beginWith';
    const NOT_BEGIN_WITH         = 'notBeginWith';
    const END_WITH               = 'endWith';
    const NOT_END_WITH           = 'notEndWith';
    const CONTAIN                = 'contain';
    const NOT_CONTAIN            = 'notContain';

    const INSTANCE_OF            = 'isInstanceOf';

    /**
     * Creates a conjunction of the given boolean expressions.
     */
    public function andX();

    /**
     * Creates a disjunction of the given boolean expressions.
     */
    public function orX();

    /**
     * Creates an equality comparison expression with the given arguments.
     *
     * @param string $field
     * @param mixed $value
     */
    public function equal($field, $value);

    /**
     * Creates an inequality comparison expression with the given arguments.
     *
     * @param string $field
     * @param mixed $value
     */
    public function notEqual($field, $value);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param mixed $value
     */
    public function lessThan($field, $value);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param mixed $value
     */
    public function lessThanOrEqual($field, $value);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param mixed $value
     */
    public function greaterThan($field, $value);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param mixed $value
     */
    public function greaterThanOrEqual($field, $value);

    /**
     * Creates an IN() expression with the given arguments.
     *
     * @param string $field
     * @param mixed $value
     */
    public function in($field, $value);

    /**
     * Creates an NOT IN() expression with the given arguments.
     *
     * @param string $field
     * @param mixed $value
     */
    public function notIn($field, $value);

    /**
     * Creates a negation expression of the given restriction.
     *
     * @param mixed $restriction
     */
    public function not($restriction);

    /**
     * Creates an IS NULL expression with the given arguments.
     *
     * @param string $field
     */
    public function isNull($field);

    /**
     * Creates an IS NOT NULL expression with the given arguments.
     *
     * @param string $field
     */
    public function isNotNull($field);

    /**
     * Creates an instance of BETWEEN() function, with the given argument.
     *
     * @param string $field
     * @param mixed $min
     * @param mixed $max
     */
    public function between($field, $min, $max);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param string $value
     */
    public function beginWith($field, $value);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param string $value
     */
    public function notBeginWith($field, $value);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param string $value
     */
    public function endWith($field, $value);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param string $value
     */
    public function notEndWith($field, $value);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param string $value
     */
    public function contain($field, $value);

    /**
     * Creates a comparison expression with the given arguments.
     *
     * @param string $field
     * @param string $value
     */
    public function notContain($field, $value);

    /**
     * Creates an instance of INSTANCE OF function, with the given arguments.
     *
     * @param string $field
     * @param mixed $value
     */
    public function isInstanceOf($field, $value);
}
