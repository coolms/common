<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\Annotation;

use Zend\Cache\Storage\StorageInterface,
    Zend\Form\Annotation\AnnotationBuilder as ZendAnnotationBuilder,
    CmsCommon\Cache\StorageProviderInterface;

/**
 * Parses a class' properties for annotations in order to create a form and
 * input filter definition.
 */
class AnnotationBuilder extends ZendAnnotationBuilder implements StorageProviderInterface
{
    use AnnotationBuilderCacheTrait,
        AnnotationBuilderTrait;

    /**
     * __construct
     *
     * @param StorageInterface $cacheStorage
     */
    public function __construct(StorageInterface $cacheStorage = null)
    {
        if (null !== $cacheStorage) {
            $this->setCacheStorage($cacheStorage);
        }
    }
}
