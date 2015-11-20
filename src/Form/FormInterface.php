<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form;

use Zend\Form\FormInterface as ZendFormInterface;

interface FormInterface extends ZendFormInterface, FieldsetInterface
{
    const RENDER_MODE_DYNAMIC = 'dynamic';
    const RENDER_MODE_STATIC  = 'static';

    /**
     * @param int $step
     * @return self
     */
    public function setPriorityStep($step);

    /**
     * Set the element group (set of fields to display and validate)
     *
     * @return self
     */
    public function setElementGroup();

    /**
     * @param bool $flag
     * @return self
     */
    public function setMergeInputFilter($flag);

    /**
     * @return bool
     */
    public function hasData();
}
