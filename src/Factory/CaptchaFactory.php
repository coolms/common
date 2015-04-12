<?php
/**
 * CoolMS2 Common Module (http://www.coolms.com/)
 *
 * @link      http://github.com/coolms/common for the canonical source repository
 * @copyright Copyright (c) 2006-2015 Altgraphic, ALC (http://www.altgraphic.com)
 * @license   http://www.coolms.com/license/new-bsd New BSD License
 * @author    Dmitry Popov <d.popov@altgraphic.com>
 */

namespace CmsCommon\Factory;

use Zend\Captcha\AdapterInterface,
    Zend\Captcha\Factory,
    Zend\ServiceManager\FactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Default CAPTCHA service factory
 *
 * @author Dmitry Popov <d.popov@altgraphic.com>
 */
class CaptchaFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $configKey = 'captcha';

    /**
     * @var array
     */
    protected $defaultCaptchaOptions = [
            'class'   => 'Figlet',
            'options' => [
                'wordLen'    => 3,
                'expiration' => 600,
                'timeout'    => 600,
            ],
        ];

    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return Factory::factory($this->getOptions($services));
    }

    /**
     * @param ServiceLocatorInterface $services
     * @return array|\Traversable|AdapterInterface
     */
    public function getOptions(ServiceLocatorInterface $services)
    {
        $config = $services->get('Config');

        if (!empty($config[$this->configKey])) {
            if (is_string($config[$this->configKey])) {
                if ($services->has($config[$this->configKey])) {
                    return $services->get($config[$this->configKey]);
                }

                $config[$this->configKey] = ['class' => $config[$this->configKey]];
            }

            return $config[$this->configKey];
        }

        return $this->defaultCaptchaOptions;
    }
}
