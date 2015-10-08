<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Factory\Form;

use RuntimeException,
    Zend\EventManager\ListenerAggregateInterface,
    Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    DoctrineModule\Service\AbstractFactory,
    CmsCommon\Form\Annotation\AnnotationBuilder,
    CmsCommon\Form\Options\FormAnnotationBuilder;

/**
 * Factory for {@see AnnotationBuilder}
 */
class AnnotationBuilderFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws RuntimeException
     * @return AnnotationBuilder
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $options FormAnnotationBuilder */
        $options = $serviceLocator->get(FormAnnotationBuilder::class);

        $cache = $serviceLocator->has($options->getCache())
            ? $serviceLocator->get($options->getCache())
            : null;

        $builder = new AnnotationBuilder($cache);

        if ($serviceLocator->has('FormElementManager')) {
            $serviceLocator->get('FormElementManager')->injectFactory($builder);
        }

        foreach ($options->getAnnotations() as $annotation) {
            $builder->getAnnotationParser()->registerAnnotation($annotation);
        }

        $events = $builder->getEventManager();
        foreach ($options->getListeners() as $listener) {
            $listener = $serviceLocator->has($listener)
                ? $serviceLocator->get($listener)
                : new $listener();

            if (!$listener instanceof ListenerAggregateInterface) {
                throw new RuntimeException(sprintf(
                    'Invalid event listener (%s) provided',
                    get_class($listener)
                ));
            }

            $events->attach($listener);
        }

        if (null !== $options->getPreserveDefinedOrder()) {
            $builder->setPreserveDefinedOrder($options->getPreserveDefinedOrder());
        }

        return $builder;
    }
}
