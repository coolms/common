<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Service;

use Zend\Form\FormInterface,
    CmsCommon\Form\FormProviderInterface,
    CmsCommon\Persistence\MapperProviderInterface,
    CmsCommon\Session\ContainerProviderInterface;

/**
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
interface DomainServiceInterface extends
    ContainerProviderInterface,
    FormProviderInterface,
    MapperProviderInterface
{
    /**
     * @return bool
     */
    public function hasForm();

    /**
     * @return bool
     */
    public function hasSessionContainer();

    /**
     * @param array|\Traversable $data
     * @param FormInterface $form
     * @param object $object
     * @return self
     */
    public function hydrate($data, FormInterface $form = null, $object = null);
}
