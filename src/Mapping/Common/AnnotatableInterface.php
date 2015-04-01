<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mapping\Common;

/**
 * Interface for the entity having an annotation
 * 
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface AnnotatableInterface
{
    /**
     * @return string
     */
    public function getAnnotation();

    /**
     * @param string $annotation
    */
    public function setAnnotation($annotation);
}
