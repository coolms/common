<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Mvc\Controller;

use Zend\Mvc\Controller\AbstractRestfulController,
    Zend\Stdlib\ResponseInterface,
    Zend\View\Model\ViewModel,
    CmsCommon\Service\DomainServiceInterface,
    CmsCommon\Service\DomainServiceProviderInterface,
    CmsCommon\Service\DomainServiceProviderTrait,
    CmsCommon\Stdlib\OptionsProviderInterface,
    CmsCommon\Stdlib\OptionsProviderTrait;

class RestfulController extends AbstractRestfulController implements
        DomainServiceProviderInterface,
        OptionsProviderInterface
{
    use DomainServiceProviderTrait,
        OptionsProviderTrait;

    /**
     * __construct
     *
     * @param DomainServiceInterface $service
     * @param RestfulControllerOptionsInterface $options
     */
    public function __construct(
        DomainServiceInterface $service,
        RestfulControllerOptionsInterface $options
    ) {
        $this->setDomainService($service);
        $this->setIdentifierName($options->getIdentifierKey());
        $this->setOptions($options);
    }

    public function getList()
    {
        
    }

    public function get($id)
    {
        return [];
    }

    public function create($data)
    {
        
    }

    public function update($id, $data)
    {
        
    }

    public function delete($id)
    {
        
    }

    public function options()
    {
        
    }
}
