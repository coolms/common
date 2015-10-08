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

use ArrayObject,
    Zend\Code\Annotation\AnnotationCollection,
    Zend\Code\Reflection\PropertyReflection,
    Zend\EventManager\Event,
    Zend\Form\Annotation\Input,
    Zend\Form\Element,
    Zend\Form\FieldsetInterface,
    Zend\Stdlib\ArrayUtils,
    CmsCommon\Form\Factory;

trait AnnotationBuilderTrait
{
    /**
     * Retrieve form factory
     *
     * Lazy-loads the default form factory if none is currently set.
     *
     * @return Factory
     */
    public function getFormFactory()
    {
        if ($this->formFactory) {
            return $this->formFactory;
        }

        $this->formFactory = new Factory();
        return $this->formFactory;
    }

    /**
     * Create a form from an object.
     *
     * @param  string|object $entity
     * @return \CmsCommon\Form\FormInterface
     */
    public function createForm($entity)
    {
        $formSpec = ArrayUtils::iteratorToArray($this->getFormSpecification($entity));
        if (!isset($formSpec['options']['merge_input_filter'])) {
            $formSpec['options']['merge_input_filter'] = true;
        }

        return $this->getFormFactory()->createForm($formSpec);
    }

    /**
     * Configure an element from annotations
     *
     * @param  AnnotationCollection $annotations
     * @param  PropertyReflection $reflection
     * @param  ArrayObject $formSpec
     * @param  ArrayObject $filterSpec
     * @return void
     *
     * @triggers checkForExclude
     * @triggers discoverName
     * @triggers configureElement
     */
    protected function configureElement($annotations, $reflection, $formSpec, $filterSpec)
    {
        // If the element is marked as exclude, return early
        if ($this->checkForExclude($annotations)) {
            return;
        }

        $events = $this->getEventManager();
        $name   = $this->discoverName($annotations, $reflection);

        $elementSpec = new ArrayObject([
            'flags' => [],
            'spec'  => [
                'name' => $name
            ],
        ]);
        $inputSpec = new ArrayObject([
            'name' => $name,
        ]);

        $event = new Event();
        $event->setParams([
            'name'        => $name,
            'elementSpec' => $elementSpec,
            'inputSpec'   => $inputSpec,
            'formSpec'    => $formSpec,
            'filterSpec'  => $filterSpec,
        ]);

        foreach ($annotations as $annotation) {
            $event->setParam('annotation', $annotation);
            $events->trigger(__FUNCTION__, $this, $event);
        }

        // Since "filters", "type", "validators" is a reserved names in the filter specification,
        // we need to add the specification without the name as the key.
        // In all other cases, though, the name is fine.
        if ($event->getParam('inputSpec')->count() > 1 || $annotations->hasAnnotation(Input::class)) {
            if ($name === 'type' || $name === 'filters' || $name === 'validators') {
                $filterSpec[] = $event->getParam('inputSpec');
            } else {
                $filterSpec[$name] = $event->getParam('inputSpec');
            }
        }

        $elementSpec = $event->getParam('elementSpec');
        $type = isset($elementSpec['spec']['type'])
            ? $elementSpec['spec']['type']
            : Element::class;

        // Compose as a fieldset or an element, based on specification type.
        // If preserve defined order is true, all elements are composed as elements to keep their ordering
        if (!$this->preserveDefinedOrder() && is_subclass_of($type, FieldsetInterface::class)) {
            if (!isset($formSpec['fieldsets'])) {
                $formSpec['fieldsets'] = [];
            }

            $formSpec['fieldsets'][] = $elementSpec;
        } else {
            if (!isset($formSpec['elements'])) {
                $formSpec['elements'] = [];
            }

            $formSpec['elements'][] = $elementSpec;
        }
    }
}
