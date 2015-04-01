<?php 
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Form\Annotation;

use Zend\Form\Annotation\AnnotationBuilder,
    CmsCommon\Cache\StorageProviderTrait;

trait AnnotationBuilderCacheTrait
{
    use StorageProviderTrait;

    /**
     * {@inheritDoc}
     */
    public function getFormSpecification($entity)
    {
        $formSpec = null;
        if ($cache = $this->getCacheStorage()) {
            // getting cache key from entity name
            $cacheKey = $this->getCacheKey($entity);

            // get the cached form, try cache first
            $formSpec = $cache->getItem($cacheKey);
        }

        if (!$formSpec) {
            $formSpec = AnnotationBuilder::getFormSpecification($entity);

            // save form to cache
            if ($cache) {
                $cache->addItem($cacheKey, $formSpec);
            }
        }

        return $formSpec;
    }

    /**
     * @param string|object $entity
     * @return string
     */
    public function getCacheKey($entity)
    {
        $name = is_object($entity) ? get_class($entity) : $entity;
        return 'forms_' . $this->normalizeObjectName($name);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function normalizeObjectName($name)
    {
        return strtolower(str_replace('\\', '_', $name));
    }
}
