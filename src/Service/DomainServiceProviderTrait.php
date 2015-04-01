<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2014 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Service;

trait DomainServiceProviderTrait
{
    /**
     * @var DomainServiceInterface
     */
    protected $domainService;

    /**
     * @return DomainServiceInterface
     */
    public function getDomainService()
    {
        return $this->domainService;
    }

    /**
     * @param DomainServiceInterface $service
     * @return self
     */
    public function setDomainService(DomainServiceInterface $service)
    {
        $this->domainService = $service;

        return $this;
    }
}
